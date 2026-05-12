import { Client, Events, GatewayIntentBits, MessageFlags } from "discord.js";
import { createServer } from "node:http";
import { env } from "./env.js";
import { chat } from "./claude.js";
import { getHistory, setHistory, clearHistory } from "./conversation.js";

const client = new Client({ intents: [GatewayIntentBits.Guilds] });

client.once(Events.ClientReady, (c) => {
  console.log(`Logged in as ${c.user.tag}`);
});

client.on(Events.InteractionCreate, async (interaction) => {
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

  if (interaction.commandName !== "ask") return;

  const question = interaction.options.getString("question", true);
  await interaction.deferReply();

  try {
    const history = getHistory(interaction.user.id, interaction.channelId);
    const next = [...history, { role: "user" as const, content: question }];
    const { history: updated, reply } = await chat(next);
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
