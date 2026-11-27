import { Button, Buttons } from '@/components/Button';
import { Eyebrow } from '@/components/Eyebrow';

export default function NotFound() {
  return (
    <section className="apex-section apex-texture-night has-carbon-bg" style={{ minHeight: '70vh' }}>
      <div className="apex-wrap apex-stack apex-stack--lg">
        <Eyebrow>Red flag · Error 404</Eyebrow>
        <h1 className="wp-block-heading has-mega-font-size">
          Off
          <br />
          <em>track.</em>
        </h1>
        <p className="is-style-apex-lede">The page you were after went off course. It may have moved, or it never existed. Get back on the racing line.</p>
        <Buttons>
          <Button href="/">Back to the start line</Button>
          <Button href="/events" variant="outline">
            Upcoming events
          </Button>
        </Buttons>
        <hr className="wp-block-separator is-style-apex-checker" />
      </div>
    </section>
  );
}
