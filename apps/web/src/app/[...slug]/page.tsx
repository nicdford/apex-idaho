import type { Metadata } from 'next';
import Image from 'next/image';
import { notFound, redirect } from 'next/navigation';
import { PageHero } from '@/components/PageHero';
import { WordPressContent } from '@/components/WordPressContent';
import { WORDPRESS_PUBLIC_URL } from '@/lib/config';
import { isWordPressOnlyPath, stripTags } from '@/lib/html';
import { getPost, getPostPaths } from '@/lib/wp';

/**
 * News posts from WordPress, at their WordPress permalink paths. Pages are
 * static routes in this app; anything else here is a 404.
 */
type Props = { params: Promise<{ slug: string[] }> };

const pathOf = (slug: string[]) => `/${slug.join('/')}`;

export async function generateStaticParams() {
  const posts = await getPostPaths();
  return posts.filter(({ path }) => path !== '/' && !isWordPressOnlyPath(path)).map(({ path }) => ({ slug: path.split('/').filter(Boolean) }));
}

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const post = await getPost(pathOf((await params).slug));
  if (!post) return {};
  return {
    title: post.title,
    description: stripTags(post.excerpt).slice(0, 180) || undefined,
    openGraph: post.image ? { images: [{ url: post.image.url }] } : undefined,
  };
}

export default async function PostPage({ params }: Props) {
  const path = pathOf((await params).slug);
  if (isWordPressOnlyPath(path)) redirect(`${WORDPRESS_PUBLIC_URL}${path}/`);

  const post = await getPost(path);
  if (!post) notFound();

  const date = new Date(post.date).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric', timeZone: 'UTC' });
  return (
    <article>
      <PageHero eyebrow="News & results" title={post.title}>
        <p className="apex-meta">
          <time dateTime={post.date}>{date}</time>
        </p>
      </PageHero>
      {post.image ? (
        <div className="apex-wrap" style={{ marginTop: 'var(--wp--preset--spacing--50)' }}>
          <Image src={post.image.url} alt={post.image.alt ?? ''} width={post.image.width} height={post.image.height} sizes="(max-width: 1400px) 100vw, 1320px" style={{ width: '100%', height: 'auto', aspectRatio: '21 / 9', objectFit: 'cover' }} priority />
        </div>
      ) : null}
      <div className="apex-wrap apex-section" style={{ paddingTop: 'var(--wp--preset--spacing--50)' }}>
        <WordPressContent html={post.html} />
      </div>
    </article>
  );
}
