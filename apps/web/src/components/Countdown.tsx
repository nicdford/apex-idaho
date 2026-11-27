'use client';

import { useSyncExternalStore } from 'react';
import Link from 'next/link';

type Props = {
  /** Milliseconds since the epoch. */
  target: number;
  label?: string;
  title?: string;
  href?: string;
  meta?: string;
};

const pad = (n: number) => String(n).padStart(2, '0');

function split(ms: number) {
  const s = Math.max(0, Math.floor(ms / 1000));
  return { d: Math.floor(s / 86400), h: Math.floor(s / 3600) % 24, m: Math.floor(s / 60) % 60, s: s % 60 };
}

/** A one-second clock as an external store (seconds since the epoch). */
function subscribe(onTick: () => void) {
  const id = window.setInterval(onTick, 1000);
  return () => window.clearInterval(id);
}
const nowSeconds = () => Math.floor(Date.now() / 1000);
const serverSeconds = () => null;

/**
 * Live countdown (WordPress: apex/countdown). The server render shows dashes
 * (time is per visitor); it ticks once a second after hydration.
 */
export function Countdown({ target, label = 'Lights out in', title, href, meta }: Props) {
  const seconds = useSyncExternalStore(subscribe, nowSeconds, serverSeconds);
  const left = seconds === null ? null : target - seconds * 1000;
  const live = left !== null && left <= 0;
  const t = split(left ?? 0);
  const units: [string, string][] = [
    [left === null ? '–' : String(t.d), 'Days'],
    [left === null ? '––' : pad(t.h), 'Hrs'],
    [left === null ? '––' : pad(t.m), 'Min'],
    [left === null ? '––' : pad(t.s), 'Sec'],
  ];

  return (
    <div className={`apex-countdown${live ? ' is-live' : ''}`}>
      {label || title ? (
        <p className="apex-countdown__label">
          {label ? <span className="apex-countdown__kicker">{label}</span> : null}
          {title && href ? (
            <Link className="apex-countdown__title" href={href}>
              {title}
            </Link>
          ) : title ? (
            <span className="apex-countdown__title">{title}</span>
          ) : null}
        </p>
      ) : null}
      <div className="apex-countdown__clock" role="timer">
        {live
          ? 'Green flag — on track now'
          : units.map(([value, unit]) => (
              <span key={unit} className="apex-countdown__unit">
                <b>{value}</b>
                <small>{unit}</small>
              </span>
            ))}
      </div>
      {meta ? <p className="apex-countdown__meta">{meta}</p> : null}
    </div>
  );
}
