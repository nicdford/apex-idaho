import type { Metadata } from 'next';
import Image from 'next/image';
import Link from 'next/link';
import { PageHero } from '@/components/PageHero';
import { stripTags } from '@/lib/html';
import { getPosts } from '@/lib/wp';

export const metadata: Metadata = {
  title: 'News',
  description: 'News and results from APEX Idaho.',
};

export default async function NewsPage() {
  const posts = await getPosts(12);

  return (
    <>
      <PageHero eyebrow="News & results" title={<>From the <strong>paddock.</strong></>} />
      <section className="apex-section has-carbon-bg" style={{ paddingTop: 'var(--wp--preset--spacing--60)' }}>
        <div className="apex-wrap">
          {posts.length ? (
            <ul className="apex-grid" role="list" style={{ listStyle: 'none', margin: 0, padding: 0 }}>
              {posts.map((post) => (
                <li key={post.id} className="apex-post-card apex-card">
                  {post.image ? (
                    <div className="apex-post-card__media">
                      <Image src={post.image.url} alt="" width={post.image.width} height={post.image.height} sizes="(max-width: 700px) 100vw, 33vw" />
                    </div>
                  ) : null}
                  <div className="apex-post-card__body">
                    <p className="apex-meta">
                      <time dateTime={post.date}>{new Date(post.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric', timeZone: 'UTC' })}</time>
                    </p>
                    <h2 className="wp-block-heading">
                      <Link href={post.path}>{post.title}</Link>
                    </h2>
                    <p className="has-smoke-color">{stripTags(post.excerpt).slice(0, 160)}</p>
                  </div>
                </li>
              ))}
            </ul>
          ) : (
            <p className="is-style-apex-lede">Nothing on the board yet. The events calendar always has what&rsquo;s next.</p>
          )}
        </div>
      </section>
    </>
  );
}
