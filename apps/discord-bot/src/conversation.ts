import type { ChatMessage } from "./claude.js";

const TTL_MS = 10 * 60 * 1000;
const MAX_MESSAGES = 30;

type Entry = { messages: ChatMessage[]; updatedAt: number };

const store = new Map<string, Entry>();

function key(userId: string, channelId: string) {
  return `${channelId}:${userId}`;
}

export function getHistory(userId: string, channelId: string): ChatMessage[] {
  const e = store.get(key(userId, channelId));
  if (!e) return [];
  if (Date.now() - e.updatedAt > TTL_MS) {
    store.delete(key(userId, channelId));
    return [];
  }
  return e.messages;
}

export function setHistory(userId: string, channelId: string, messages: ChatMessage[]) {
  const trimmed = messages.slice(-MAX_MESSAGES);
  store.set(key(userId, channelId), { messages: trimmed, updatedAt: Date.now() });
}

export function clearHistory(userId: string, channelId: string) {
  store.delete(key(userId, channelId));
}
