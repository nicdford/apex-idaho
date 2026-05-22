import { env } from "./env.js";
import { listEventsCached } from "./wp-client.js";

export type EventContext = { eventId: number; title: string };

const overrides = new Map<string, EventContext>();

export function setEventContext(channelId: string, ctx: EventContext) {
  overrides.set(channelId, ctx);
}

export function clearEventContext(channelId: string) {
  overrides.delete(channelId);
}

export async function resolveEventContext(channelId: string): Promise<EventContext | undefined> {
  const override = overrides.get(channelId);
  if (override) return override;

  const defaultEventId = env.channelEventDefaults.get(channelId);
  if (defaultEventId == null) return undefined;

  try {
    const events = await listEventsCached();
    const match = events.find((e) => e.id === defaultEventId);
    if (match) return { eventId: match.id, title: match.title };
  } catch (err) {
    console.error("resolveEventContext: failed to load events", err);
  }
  return { eventId: defaultEventId, title: `event #${defaultEventId}` };
}
