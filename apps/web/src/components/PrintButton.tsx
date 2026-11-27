'use client';

/** Prints the page (the print stylesheet in app.css drops the site chrome). */
export function PrintButton({ children }: { children: React.ReactNode }) {
  return (
    <div className="wp-block-button is-style-outline apex-print-hide">
      <button type="button" className="wp-block-button__link wp-element-button" onClick={() => window.print()}>
        {children}
      </button>
    </div>
  );
}
