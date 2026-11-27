import { rewriteWordPressHtml } from '@/lib/html';

/**
 * HTML rendered by WordPress (page, post and event content). Editors write it
 * in the block editor; links are re-pointed at this site, and the block
 * classes are styled by tokens.css / theme.css / app.css (.apex-prose).
 */
export function WordPressContent({ html, className = '' }: { html: string; className?: string }) {
  if (!html.trim()) return null;
  return <div className={`apex-prose ${className}`.trim()} dangerouslySetInnerHTML={{ __html: rewriteWordPressHtml(html) }} />;
}
