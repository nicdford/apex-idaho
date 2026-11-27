import type { ReactNode } from 'react';

export function Eyebrow({ children, center = false, className = '' }: { children: ReactNode; center?: boolean; className?: string }) {
  return <p className={`is-style-apex-eyebrow${center ? ' has-text-align-center' : ''} ${className}`.trim()}>{children}</p>;
}
