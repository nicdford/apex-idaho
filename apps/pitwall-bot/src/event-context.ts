export type EventContext = { eventId: number; title: string };

const store = new Map<string, EventContext>();

export function setEventContext(channelId: string, ctx: EventContext) {
  store.set(channelId, ctx);
}

export function getEventContext(channelId: string): EventContext | undefined {
  return store.get(channelId);
}

export function clearEventContext(channelId: string) {
  store.delete(channelId);
}
