'use client';

import { useEffect, useRef, useState } from 'react';
import { usePathname } from 'next/navigation';
import type { NavItem } from '@/lib/config';
import { SmartLink } from './SmartLink';
import { Button, Buttons } from './Button';

/**
 * Primary navigation. Inline above 1100px; below that a full-screen menu,
 * like the WordPress header's Navigation block overlay.
 */
export function MainNav({ items }: { items: NavItem[] }) {
  const pathname = usePathname();
  const [open, setOpen] = useState(false);
  const toggleRef = useRef<HTMLButtonElement>(null);
  const closeRef = useRef<HTMLButtonElement>(null);
  const overlayRef = useRef<HTMLDivElement>(null);

  const isCurrent = (href: string) => href.startsWith('/') && (pathname === href || pathname.startsWith(`${href}/`));

  // Shadow under the sticky header once the page scrolls.
  useEffect(() => {
    const root = document.documentElement;
    const update = () => root.classList.toggle('is-scrolled', window.scrollY > 8);
    update();
    window.addEventListener('scroll', update, { passive: true });
    return () => window.removeEventListener('scroll', update);
  }, []);

  // Close the menu after navigating (state adjusted during render, not in an effect).
  const [lastPath, setLastPath] = useState(pathname);
  if (pathname !== lastPath) {
    setLastPath(pathname);
    setOpen(false);
  }

  useEffect(() => {
    const root = document.documentElement;
    if (!open) {
      root.classList.remove('has-modal-open');
      return;
    }
    root.classList.add('has-modal-open');
    closeRef.current?.focus();
    const toggle = toggleRef.current;

    const onKey = (e: KeyboardEvent) => {
      if (e.key === 'Escape') {
        setOpen(false);
        return;
      }
      // Keep Tab inside the menu while it is open.
      if (e.key === 'Tab' && overlayRef.current) {
        const focusable = overlayRef.current.querySelectorAll<HTMLElement>('a[href], button:not([disabled])');
        if (!focusable.length) return;
        const first = focusable[0];
        const last = focusable[focusable.length - 1];
        if (e.shiftKey && document.activeElement === first) {
          e.preventDefault();
          last.focus();
        } else if (!e.shiftKey && document.activeElement === last) {
          e.preventDefault();
          first.focus();
        }
      }
    };
    document.addEventListener('keydown', onKey);
    return () => {
      document.removeEventListener('keydown', onKey);
      root.classList.remove('has-modal-open');
      toggle?.focus();
    };
  }, [open]);

  return (
    <nav aria-label="Main">
      <ul className="apex-nav__list">
        {items.map((item) => (
          <li key={item.href}>
            <SmartLink href={item.href} aria-current={isCurrent(item.href) ? 'page' : undefined}>
              {item.label}
            </SmartLink>
          </li>
        ))}
      </ul>

      <button ref={toggleRef} type="button" className="apex-nav__toggle" aria-expanded={open} aria-controls="apex-menu" onClick={() => setOpen(true)}>
        <svg width="30" height="30" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
          <path d="M3 7h18v2H3zM3 15h18v2H3z" fill="currentColor" />
        </svg>
        <span className="screen-reader-text">Open menu</span>
      </button>

      <div ref={overlayRef} id="apex-menu" className="apex-nav__overlay" role="dialog" aria-modal="true" aria-label="Menu" hidden={!open}>
        <button ref={closeRef} type="button" className="apex-nav__toggle apex-nav__close" onClick={() => setOpen(false)}>
          <svg width="30" height="30" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
            <path d="M5.6 4.2 12 10.6l6.4-6.4 1.4 1.4-6.4 6.4 6.4 6.4-1.4 1.4-6.4-6.4-6.4 6.4-1.4-1.4 6.4-6.4-6.4-6.4z" fill="currentColor" />
          </svg>
          <span className="screen-reader-text">Close menu</span>
        </button>
        <ol>
          {items.map((item) => (
            <li key={item.href}>
              <SmartLink href={item.href} aria-current={isCurrent(item.href) ? 'page' : undefined} onClick={() => setOpen(false)}>
                {item.label}
              </SmartLink>
            </li>
          ))}
        </ol>
        <Buttons>
          <Button href="/events">Get tickets</Button>
        </Buttons>
      </div>
    </nav>
  );
}
