import { Fragment } from 'react';

/** Plain text with **bold** spans, for rule lists kept in src/content. */
export function Inline({ text }: { text: string }) {
  return (
    <>
      {text.split('**').map((part, i) => (i % 2 ? <strong key={i}>{part}</strong> : <Fragment key={i}>{part}</Fragment>))}
    </>
  );
}
