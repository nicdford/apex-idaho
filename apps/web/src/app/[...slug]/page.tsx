import type { Metadata } from 'next';
import Image from 'next/image';
import { notFound, redirect } from 'next/navigation';
import { Eyebrow } from '@/components/Eyebrow';
import { PageHero } from '@/components/PageHero';
import { WordPressContent } from '@/components/WordPressContent';
import { WORDPRESS_PUBLIC_URL } from '@/lib/config';
import { isWordPressOnlyPath, stripTags } from '@/lib/html';
import { getAllEntryPaths, getPage, getPost, type Entry } from '@/lib/wp';

type Props = { params: Promise<{ slug: string[] }> };

/** Paths with their own route here (the WordPress page of the same name is not used). */
const OWN_ROUTES = new Set(['/events', '/sponsors', '/media', '/news', '/home']);

async function resolve(slug: string[]): Promise<Entry | null> {
  const path = slug.join('/');
  return (await getPage(path)) ?? (await getPost(path));
}

export async function generateStaticParams() {
  const entries = await getAllEntryPaths();
  return entries
    .filter(({ path }) => path !== '/' && !OWN_ROUTES.has(path) && !isWordPressOnlyPath(path))
    .map(({ path }) => ({ slug: path.split('/').filter(Boolean) }));
}

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const entry = await resolve((await params).slug);
  if (!entry) return {};
  return {
    title: entry.title,
    description: stripTags(entry.excerpt).slice(0, 180) || undefined,
    openGraph: entry.image ? { images: [{ url: entry.image.url }] } : undefined,
  };
}

export default async function WordPressPage({ params }: Props) {
  const { slug } = await params;
  const path = `/${slug.join('/')}`;
  if (isWordPressOnlyPath(path)) redirect(`${WORDPRESS_PUBLIC_URL}${path}/`);

  const entry = await resolve(slug);
  if (!entry) notFound();

  if (entry.type === 'post') {
    const date = new Date(entry.date).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric', timeZone: 'UTC' });
    return (
      <article>
        <PageHero eyebrow="News & results" title={entry.title}>
          <p className="apex-meta">
            <time dateTime={entry.date}>{date}</time>
          </p>
        </PageHero>
        {entry.image ? (
          <div className="apex-wrap" style={{ marginTop: 'var(--wp--preset--spacing--50)' }}>
            <Image src={entry.image.url} alt={entry.image.alt ?? ''} width={entry.image.width} height={entry.image.height} sizes="(max-width: 1400px) 100vw, 1320px" style={{ width: '100%', height: 'auto', aspectRatio: '21 / 9', objectFit: 'cover' }} priority />
          </div>
        ) : null}
        <div className="apex-wrap apex-section" style={{ paddingTop: 'var(--wp--preset--spacing--50)' }}>
          <WordPressContent html={entry.html} />
        </div>
      </article>
    );
  }

  return (
    <>
      <PageHero eyebrow="APEX Idaho" title={entry.title} />
      <div className="apex-wrap apex-section" style={{ paddingTop: 'var(--wp--preset--spacing--60)' }}>
        {entry.html.trim() ? <WordPressContent html={entry.html} /> : <Eyebrow>Nothing here yet</Eyebrow>}
      </div>
    </>
  );
}
