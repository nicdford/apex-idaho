function required(name: string): string {
  const v = process.env[name];
  if (!v) throw new Error(`Missing required env var: ${name}`);
  return v;
}

export const env = {
  discordToken: required("DISCORD_TOKEN"),
  discordClientId: required("DISCORD_CLIENT_ID"),
  discordGuildId: process.env.DISCORD_GUILD_ID || "",
  allowedChannelIds: (process.env.ALLOWED_CHANNEL_IDS || "")
    .split(",")
    .map((s) => s.trim())
    .filter(Boolean),
  anthropicApiKey: required("ANTHROPIC_API_KEY"),
  anthropicModel: process.env.ANTHROPIC_MODEL || "claude-sonnet-4-6",
  wpBaseUrl: required("WP_BASE_URL").replace(/\/$/, ""),
  wpApiKey: required("WP_API_KEY"),
  port: Number(process.env.PORT || 8080),
};
