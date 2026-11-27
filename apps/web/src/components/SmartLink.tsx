import Link from 'next/link';
import type { ComponentProps, ReactNode } from 'react';

type Props = Omit<ComponentProps<'a'>, 'href'> & { href: string; children: ReactNode };

/**
 * Next <Link> for paths on this site, a plain <a> for everything else
 * (WordPress-served pages like checkout, other sites, mailto:).
 */
export function SmartLink({ href, children, ...rest }: Props) {
  if (href.startsWith('/') && !href.startsWith('//')) {
    return (
      <Link href={href} {...rest}>
        {children}
      </Link>
    );
  }
  const external = /^https?:\/\//.test(href);
  return (
    <a href={href} {...(external ? { rel: 'noopener' } : {})} {...rest}>
      {children}
    </a>
  );
}
