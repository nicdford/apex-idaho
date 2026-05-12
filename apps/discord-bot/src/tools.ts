import type Anthropic from "@anthropic-ai/sdk";
import { wp } from "./wp-client.js";

export const tools: Anthropic.Tool[] = [
  {
    name: "list_events",
    description:
      "List all events that have tickets configured. Returns id, title, start, end. Use this to disambiguate when the user references an event by name, or when you need an event_id for another tool.",
    input_schema: { type: "object", properties: {}, required: [] },
  },
  {
    name: "list_event_tickets",
    description:
      "List ticket types for an event, including capacity, sold, pending, and available counts. Use for capacity questions like 'how many driver spots are left'.",
    input_schema: {
      type: "object",
      properties: {
        event_id: { type: "number", description: "Event post ID" },
      },
      required: ["event_id"],
    },
  },
  {
    name: "list_event_attendees",
    description:
      "List attendees for an event. Filter by ticket_id (for driver-only / VIP-only views) and/or status (comma-separated, e.g. 'completed,processing'). Set include_meta=true to include attendee meta fields like t-shirt size.",
    input_schema: {
      type: "object",
      properties: {
        event_id: { type: "number" },
        ticket_id: { type: "number", description: "Optional: only attendees on this ticket type" },
        status: { type: "string", description: "Optional: comma-separated order statuses (e.g. 'completed' or 'completed,processing')" },
        include_meta: { type: "boolean", description: "Include attendee meta fields. Default false." },
      },
      required: ["event_id"],
    },
  },
  {
    name: "search_attendees",
    description:
      "Search attendees by name or email substring across all events (or one event if event_id given). Use to answer 'is X registered'. Case-insensitive substring match.",
    input_schema: {
      type: "object",
      properties: {
        q: { type: "string" },
        event_id: { type: "number", description: "Optional: limit to one event" },
        include_meta: { type: "boolean" },
      },
      required: ["q"],
    },
  },
];

export async function runTool(name: string, input: Record<string, unknown>): Promise<unknown> {
  switch (name) {
    case "list_events":
      return await wp.listEvents();
    case "list_event_tickets":
      return await wp.listEventTickets(Number(input.event_id));
    case "list_event_attendees":
      return await wp.listEventAttendees(Number(input.event_id), {
        ticketId: input.ticket_id != null ? Number(input.ticket_id) : undefined,
        status: input.status as string | undefined,
        includeMeta: Boolean(input.include_meta),
      });
    case "search_attendees":
      return await wp.searchAttendees(String(input.q), {
        eventId: input.event_id != null ? Number(input.event_id) : undefined,
        includeMeta: Boolean(input.include_meta),
      });
    default:
      throw new Error(`Unknown tool: ${name}`);
  }
}
