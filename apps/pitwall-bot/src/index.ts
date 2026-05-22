import { Client, Events, GatewayIntentBits, MessageFlags } from "discord.js";
import { createServer } from "node:http";
import { env } from "./env.js";
import { chat } from "./claude.js";
import { getHistory, setHistory, clearHistory } from "./conversation.js";
import {
  resolveEventContext,
  setEventContext,
  clearEventContext,
} from "./event-context.js";
import { listEventsCached } from "./wp-client.js";

const client = new Client({ intents: [GatewayIntentBits.Guilds] });

client.once(Events.ClientReady, (c) => {
  console.log(`Logged in as ${c.user.tag}`);
});

client.on(Events.InteractionCreate, async (interaction) => {
  if (interaction.isAutocomplete()) {
    if (interaction.commandName !== "setevent") return;
    try {
      const focused = interaction.options.getFocused().toLowerCase();
      const events = await listEventsCached();
      const matches = (focused
        ? events.filter((e) => e.title.toLowerCase().includes(focused))
        : events
      ).slice(0, 25);
      await interaction.respond(
        matches.map((e) => ({ name: e.title.slice(0, 100), value: String(e.id) }))
      );
    } catch (err) {
      console.error("autocomplete error", err);
      try {
        await interaction.respond([]);
      } catch {}
    }
    return;
  }

  if (!interaction.isChatInputCommand()) return;

  if (env.allowedChannelIds.length && !env.allowedChannelIds.includes(interaction.channelId)) {
    await interaction.reply({
      content: "This bot isn't authorized in this channel.",
      flags: MessageFlags.Ephemeral,
    });
    return;
  }

  if (interaction.commandName === "reset") {
    clearHistory(interaction.user.id, interaction.channelId);
    await interaction.reply({ content: "Conversation cleared.", flags: MessageFlags.Ephemeral });
    return;
  }

  if (interaction.commandName === "setevent") {
    const eventIdStr = interaction.options.getString("event", true);
    const eventId = Number(eventIdStr);
    if (!Number.isFinite(eventId)) {
      await interaction.reply({
        content: "Invalid event selection.",
        flags: MessageFlags.Ephemeral,
      });
      return;
    }
    const events = await listEventsCached();
    const match = events.find((e) => e.id === eventId);
    if (!match) {
      await interaction.reply({
        content: "That event wasn't found. Try again and pick from the autocomplete.",
        flags: MessageFlags.Ephemeral,
      });
      return;
    }
    setEventContext(interaction.channelId, { eventId: match.id, title: match.title });
    clearHistory(interaction.user.id, interaction.channelId);
    await interaction.reply({
      content: `This channel is now focused on **${match.title}**. \`/ask\` questions will default to it. Use \`/clearevent\` to unset.`,
    });
    return;
  }

  if (interaction.commandName === "clearevent") {
    clearEventContext(interaction.channelId);
    clearHistory(interaction.user.id, interaction.channelId);
    await interaction.reply({
      content: "Event lock removed for this channel.",
      flags: MessageFlags.Ephemeral,
    });
    return;
  }

  if (interaction.commandName !== "ask") return;

  const question = interaction.options.getString("question", true);
  await interaction.deferReply();

  try {
    const history = getHistory(interaction.user.id, interaction.channelId);
    const next = [...history, { role: "user" as const, content: question }];
    const eventContext = await resolveEventContext(interaction.channelId);
    const { history: updated, reply } = await chat(next, eventContext);
    setHistory(interaction.user.id, interaction.channelId, updated);

    const chunks = splitForDiscord(reply);
    await interaction.editReply(chunks[0]);
    for (const c of chunks.slice(1)) {
      await interaction.followUp(c);
    }
  } catch (err) {
    console.error(err);
    const msg = err instanceof Error ? err.message : String(err);
    await interaction.editReply(`Error: ${msg.slice(0, 1800)}`);
  }
});

function splitForDiscord(text: string): string[] {
  const MAX = 1900;
  if (text.length <= MAX) return [text];
  const out: string[] = [];
  let remaining = text;
  while (remaining.length > MAX) {
    let cut = remaining.lastIndexOf("\n", MAX);
    if (cut < MAX * 0.5) cut = MAX;
    out.push(remaining.slice(0, cut));
    remaining = remaining.slice(cut).trimStart();
  }
  if (remaining) out.push(remaining);
  return out;
}

createServer((_req, res) => {
  res.writeHead(200, { "Content-Type": "text/plain" });
  res.end("ok");
}).listen(env.port, () => {
  console.log(`Healthcheck listening on ${env.port}`);
});

client.login(env.discordToken);
