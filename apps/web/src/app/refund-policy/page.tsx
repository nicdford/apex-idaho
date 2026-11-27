import type { Metadata } from 'next';
import { Button, Buttons } from '@/components/Button';
import { PageHero } from '@/components/PageHero';
import { refundPolicy } from '@/content/refund-policy';

export const metadata: Metadata = {
  title: 'Refund Policy',
  description: 'APEX Idaho ticket refund policy.',
};

export default function RefundPolicyPage() {
  return (
    <>
      <PageHero eyebrow="Tickets" title={<>Refund <strong>policy</strong></>} />
      <section className="apex-section has-carbon-bg">
        <div className="apex-wrap apex-stack apex-stack--lg" style={{ maxWidth: 'calc(var(--wp--style--global--content-size) + 2 * var(--wp--style--root--padding-left))' }}>
          {refundPolicy.map((p) => (
            <p key={p} className="is-style-apex-lede">
              {p}
            </p>
          ))}
          <Buttons>
            <Button href="/tech-rules" variant="outline">
              Tech &amp; rules
            </Button>
          </Buttons>
        </div>
      </section>
    </>
  );
}
