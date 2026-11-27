import type { ReactNode } from 'react';
import { SmartLink } from './SmartLink';

type Variant = 'arrow' | 'outline' | 'plain';

const variantClass: Record<Variant, string> = {
  arrow: 'is-style-apex-arrow',
  outline: 'is-style-outline',
  plain: '',
};

/** Slanted button, same markup and classes as the WordPress Button block. */
export function Button({ href, children, variant = 'arrow' }: { href: string; children: ReactNode; variant?: Variant }) {
  return (
    <div className={`wp-block-button ${variantClass[variant]}`.trim()}>
      <SmartLink className="wp-block-button__link wp-element-button" href={href}>
        {children}
      </SmartLink>
    </div>
  );
}

export function Buttons({ children, className = '' }: { children: ReactNode; className?: string }) {
  return <div className={`wp-block-buttons ${className}`.trim()}>{children}</div>;
}
