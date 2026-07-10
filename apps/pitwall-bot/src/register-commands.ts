import {
  InteractionContextType,
  REST,
  Routes,
  SlashCommandBuilder,
} from "discord.js";
import { env } from "./env.js";

const ALL_CONTEXTS = [
  InteractionContextType.Guild,
  InteractionContextType.BotDM,
  InteractionContextType.PrivateChannel,
];

const commands = [
  new SlashCommandBuilder()
    .setName("ask")
    .setDescription("Ask about attendees, tickets, or registrations")
    .setContexts(ALL_CONTEXTS)
    .addStringOption((o) =>
      o.setName("question").setDescription("Your question").setRequired(true)
    )
    .toJSON(),
  new SlashCommandBuilder()
    .setName("reset")
    .setDescription("Clear your conversation history with the bot")
    .setContexts(ALL_CONTEXTS)
    .toJSON(),
  new SlashCommandBuilder()
    .setName("setevent")
    .setDescription("Lock this channel to a specific event so /ask defaults to it")
    .setContexts(ALL_CONTEXTS)
    .addStringOption((o) =>
      o
        .setName("event")
        .setDescription("Start typing the event name")
        .setRequired(true)
        .setAutocomplete(true)
    )
    .toJSON(),
  new SlashCommandBuilder()
    .setName("clearevent")
    .setDescription("Remove this channel's event lock")
    .setContexts(ALL_CONTEXTS)
    .toJSON(),
];

async function main() {
  const rest = new REST({ version: "10" }).setToken(env.discordToken);
  if (env.discordGuildId) {
    await rest.put(
      Routes.applicationGuildCommands(env.discordClientId, env.discordGuildId),
      { body: commands }
    );
    console.log(`Registered ${commands.length} guild commands.`);
  } else {
    await rest.put(Routes.applicationCommands(env.discordClientId), {
      body: commands,
    });
    console.log(`Registered ${commands.length} global commands (may take up to 1h to appear).`);
  }
}

main().catch((e) => {
  console.error(e);
  process.exit(1);
});
