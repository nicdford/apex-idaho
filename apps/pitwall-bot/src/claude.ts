import Anthropic from "@anthropic-ai/sdk";
import { env } from "./env.js";
import { tools, runTool } from "./tools.js";

const client = new Anthropic({ apiKey: env.anthropicApiKey });

const SYSTEM_PROMPT = `You are Pitwall, an AI assistant for event organizers running WordPress + The Events Calendar + WooCommerce. You answer questions about event attendees, tickets, registrations, and capacity by calling tools against the organizer's WordPress site.

Formatting (Discord-flavored Markdown, NOT GitHub Markdown):
- Discord does NOT render Markdown tables. Never use \`| col | col |\` — the pipes show as raw text. For tabular data, use a fenced code block with space-padded columns so monospace alignment carries the structure.
- Use \`## Heading\` for section titles, \`**bold**\` for emphasis on names/counts, and numbered (\`1.\`) or bulleted (\`- \`) lists for collections.
- Use \`> note\` blockquotes for caveats or assumptions.
- Use inline \`backticks\` for ticket names, statuses, IDs.
- Keep replies under ~1500 chars where possible; long messages auto-split.

Content:
- Be concise. Lead with the answer, then supporting detail.
- For lists of more than ~15 people, summarize counts and show the first 10–15 names with key info; offer to filter further.
- If the system message specifies a current channel event, assume questions refer to that event and pass its event_id to tools by default. Only call list_events for disambiguation when the user explicitly names a different event.
- Otherwise, if the user names an event that isn't unique or is ambiguous, call list_events and ask which one.
- "Driver", "VIP", etc. are ticket types — look at list_event_tickets and match by name (case-insensitive substring).
- "Paid" / "registered" typically means order_status=completed. Mention this assumption if results seem off.
- For capacity questions ("how many spots left"), use list_event_tickets and report available vs capacity.
- For meta questions (t-shirt size, etc.), call list_event_attendees with include_meta=true.
- If you don't have enough info (e.g. user asks "is nic registered" without an event), ask a follow-up. The user can only reply via another /ask command, so end follow-up questions with a hint like "(reply with /ask)".
- Never invent attendees, counts, or events. If a tool returns no matches, say so.
- Don't expose security_code or full email lists unless explicitly asked.`;

export type ChatMessage = Anthropic.MessageParam;

export type ChatEventContext = { eventId: number; title: string };

export async function chat(
  history: ChatMessage[],
  eventContext?: ChatEventContext
): Promise<{ history: ChatMessage[]; reply: string }> {
  const messages: ChatMessage[] = [...history];

  const system = eventContext
    ? `${SYSTEM_PROMPT}\n\nCurrent channel event: "${eventContext.title}" (event_id=${eventContext.eventId}). Default tool calls that take event_id to this value unless the user explicitly references another event.`
    : SYSTEM_PROMPT;

  for (let step = 0; step < 8; step++) {
    const response = await client.messages.create({
      model: env.anthropicModel,
      max_tokens: 2048,
      system,
      tools,
      messages,
    });

    messages.push({ role: "assistant", content: response.content });

    if (response.stop_reason !== "tool_use") {
      const text = response.content
        .filter((b): b is Anthropic.TextBlock => b.type === "text")
        .map((b) => b.text)
        .join("\n")
        .trim();
      return { history: messages, reply: text || "(no response)" };
    }

    const toolUses = response.content.filter(
      (b): b is Anthropic.ToolUseBlock => b.type === "tool_use"
    );

    const toolResults: Anthropic.ToolResultBlockParam[] = [];
    for (const tu of toolUses) {
      try {
        const result = await runTool(tu.name, tu.input as Record<string, unknown>);
        toolResults.push({
          type: "tool_result",
          tool_use_id: tu.id,
          content: JSON.stringify(result),
        });
      } catch (err) {
        toolResults.push({
          type: "tool_result",
          tool_use_id: tu.id,
          is_error: true,
          content: err instanceof Error ? err.message : String(err),
        });
      }
    }

    messages.push({ role: "user", content: toolResults });
  }

  return { history: messages, reply: "Sorry — exceeded tool-call budget without an answer. Try rephrasing." };
}
