import type { Metadata } from 'next';
import { Button, Buttons } from '@/components/Button';
import { Inline } from '@/components/Inline';
import { PageHero } from '@/components/PageHero';
import { PrintButton } from '@/components/PrintButton';
import { codeOfConduct, techRules, updated } from '@/content/tech-rules';

export const metadata: Metadata = {
  title: 'Tech & Rules',
  description: 'Official tech rules and car requirements for every APEX Idaho event, and the driver code of conduct.',
};

const updatedLabel = new Date(`${updated}T12:00:00Z`).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric', timeZone: 'UTC' });

export default function TechRulesPage() {
  return (
    <>
      <PageHero eyebrow={`Official · Updated ${updatedLabel}`} title={<>Tech &amp; <strong>rules</strong></>} lede="Tech rules and car requirements for every APEX Idaho event, and the code of conduct every driver follows." into="paper">
        <Buttons>
          <PrintButton>Print the rules sheet</PrintButton>
        </Buttons>
      </PageHero>

      <section className="apex-section is-style-section-paper">
        <div className="apex-wrap apex-split" style={{ alignItems: 'start' }}>
          <div className="apex-stack has-white-background-color apex-rules-card">
            <h2 className="wp-block-heading has-x-large-font-size">All event tech rules and car requirements</h2>
            <ol className="apex-rules" role="list">
              {techRules.map((rule) => {
                const Sub = rule.ordered ? 'ol' : 'ul';
                return (
                  <li key={rule.text}>
                    <Inline text={rule.text} />
                    {rule.items ? (
                      <Sub>
                        {rule.items.map((item) => (
                          <li key={item}>
                            <Inline text={item} />
                          </li>
                        ))}
                      </Sub>
                    ) : null}
                  </li>
                );
              })}
            </ol>
          </div>
          <div className="apex-stack has-white-background-color apex-rules-card">
            <h2 className="wp-block-heading has-x-large-font-size">Driver code of conduct</h2>
            <ul className="wp-block-list is-style-apex-diamonds apex-conduct">
              {codeOfConduct.map((item) => (
                <li key={item}>
                  <Inline text={item} />
                </li>
              ))}
            </ul>
            <Buttons className="apex-print-hide">
              <Button href="/events">Find an event</Button>
            </Buttons>
          </div>
        </div>
      </section>
    </>
  );
}
