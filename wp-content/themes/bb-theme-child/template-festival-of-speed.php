<?php
/**
 * Template Name: Festival of Speed
 */

get_header();
?>

<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow+Condensed:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
  :root {
    --pink:   #e8197d;
    --orange: #f97316;
    --grad:   linear-gradient(135deg, #e8197d 0%, #f97316 100%);
  }

  .fos-page * { box-sizing: border-box; }
  .fos-page { font-family: 'Barlow Condensed', sans-serif; }

  /* ── Hero ── */
  .hero-photo-bg {
    position: relative;
    background-color: #ffffff;
  }

  .hero-img-wrap {
    position: relative;
    flex: 1 1 480px;
    min-height: 420px;
    overflow: hidden;
    clip-path: polygon(8% 0, 100% 0, 100% 100%, 0 100%);
  }
  .hero-img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center 40%;
    display: block;
  }
  .hero-img-wrap::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(to right, rgba(255,255,255,0.5) 0%, transparent 30%),
                linear-gradient(to top, rgba(255,255,255,0.3) 0%, transparent 30%);
  }

  @media (max-width: 768px) {
    .hero-img-wrap {
      clip-path: none;
      min-height: 260px;
      flex: 1 1 100%;
    }
  }

  /* ── Diagonal cuts ── */
  .clip-diagonal-bottom { clip-path: polygon(0 0, 100% 0, 100% 88%, 0 100%); }

  /* ── Gradient accent bar ── */
  .accent-bar { width: 50px; height: 4px; background: var(--grad); margin-bottom: 1rem; }

  /* ── Animations ── */
  @keyframes slideUp {
    from { opacity: 0; transform: translateY(40px); }
    to   { opacity: 1; transform: translateY(0); }
  }
  @keyframes fadeIn {
    from { opacity: 0; }
    to   { opacity: 1; }
  }
  .reveal-1 { animation: slideUp 0.7s ease forwards; opacity: 0; }
  .reveal-2 { animation: slideUp 0.7s 0.15s ease forwards; opacity: 0; }
  .reveal-3 { animation: slideUp 0.7s 0.3s ease forwards; opacity: 0; }
  .reveal-4 { animation: slideUp 0.7s 0.45s ease forwards; opacity: 0; }
  .reveal-5 { animation: fadeIn 0.7s 0.6s ease forwards; opacity: 0; }

  /* ── Accordion ── */
  .accordion-content { max-height: 0; overflow: hidden; transition: max-height 0.35s ease; }
  .accordion-content.open { max-height: 600px; }
  .accordion-trigger.open .accordion-chevron { transform: rotate(180deg); }
  .accordion-chevron { transition: transform 0.3s ease; }

  /* ── Perk cards ── */
  .perk-card {
    background: #ffffff;
    border: 1px solid rgba(0,0,0,0.08);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }
  .perk-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 30px rgba(232,25,125,0.15);
  }

  /* ── Hotel cards ── */
  .hotel-card {
    background: #ffffff;
    border: 1px solid rgba(0,0,0,0.08);
    transition: box-shadow 0.2s ease;
  }
  .hotel-card:hover { box-shadow: 0 0 0 2px var(--pink); }

  /* ── Sponsor box ── */
  .sponsor-box {
    border: 1px solid rgba(0,0,0,0.08);
    transition: border-color 0.2s ease;
  }
  .sponsor-box:hover { border-color: var(--pink); }

  /* ── Buttons ── */
  .btn-primary {
    background: var(--grad);
    color: white;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 1.15rem;
    font-weight: 700;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    padding: 0.85rem 2rem;
    display: inline-block;
    transition: opacity 0.2s ease, transform 0.15s ease;
    clip-path: polygon(0 0, calc(100% - 10px) 0, 100% 10px, 100% 100%, 10px 100%, 0 calc(100% - 10px));
  }
  .btn-primary:hover { opacity: 0.88; transform: translateY(-2px); }

  .btn-outline {
    background: transparent;
    color: var(--pink);
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 1.15rem;
    font-weight: 700;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    padding: 0.85rem 2rem;
    display: inline-block;
    border: 2px solid var(--pink);
    transition: background 0.2s ease, color 0.2s ease, transform 0.15s ease;
    clip-path: polygon(0 0, calc(100% - 10px) 0, 100% 10px, 100% 100%, 10px 100%, 0 calc(100% - 10px));
  }
  .btn-outline:hover {
    background: var(--pink);
    color: white;
    transform: translateY(-2px);
  }

  /* ── Step number watermark ── */
  .step-num {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 3rem;
    line-height: 1;
    background: var(--grad);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    opacity: 0.15;
    position: absolute;
    top: 0.5rem;
    right: 1rem;
  }

  /* ── Light section bg ── */
  .bg-blush { background: #fff5f9; }
</style>

<div class="fos-page">

  <!-- ═══════════════════════════════════════════
       HERO
  ═══════════════════════════════════════════ -->
  <section class="hero-photo-bg clip-diagonal-bottom" style="overflow:hidden;display:flex;align-items:stretch;min-height:580px;">

    <!-- Left: text content -->
    <div style="flex:1 1 480px;padding:6rem 2.5rem 8rem 2.5rem;display:flex;flex-direction:column;justify-content:center;align-items:flex-end;position:relative;z-index:1;text-align:right;">
      <div style="max-width:560px;width:100%;">

        <!-- Pre-title -->
        <p class="reveal-1" style="font-family:'Barlow Condensed',sans-serif;font-weight:600;letter-spacing:0.35em;font-size:1.1rem;background:var(--grad);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;text-transform:uppercase;margin:0 0 1rem;">
          APEX Idaho Presents
        </p>

        <!-- Logo -->
        <img class="reveal-2" src="https://apex-idaho.com/wp-content/uploads/2024/02/MVFOS-Stacked.png" alt="Magic Valley Festival of Speed" style="max-width:400px;width:100%;height:auto;display:block;margin:0 0 1.5rem;">

        <!-- Event meta -->
        <div class="reveal-3" style="display:flex;align-items:center;justify-content:flex-end;gap:1.5rem;margin-bottom:1.75rem;flex-wrap:wrap;">
          <div style="display:flex;align-items:center;gap:0.5rem;">
            <span style="background:var(--grad);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;font-size:1.2rem;">&#9632;</span>
            <span style="font-family:'Barlow Condensed',sans-serif;font-weight:600;letter-spacing:0.15em;color:#111;font-size:1.3rem;text-transform:uppercase;">May 23–25</span>
          </div>
          <div style="width:1px;height:20px;background:rgba(0,0,0,0.15);"></div>
          <div style="display:flex;align-items:center;gap:0.5rem;">
            <span style="background:var(--grad);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;font-size:1.2rem;">&#9632;</span>
            <span style="font-family:'Barlow Condensed',sans-serif;font-weight:600;letter-spacing:0.15em;color:#111;font-size:1.3rem;text-transform:uppercase;">Twin Falls, ID</span>
          </div>
        </div>

        <!-- Tagline -->
        <p class="reveal-4" style="font-family:'Barlow Condensed',sans-serif;font-weight:400;font-size:1.4rem;color:rgba(0,0,0,0.55);line-height:1.5;margin:0 0 2rem;">
          Three days of drift, grip, and full-throttle competition at one of Idaho's premier motorsport events. All skill levels welcome.
        </p>

        <!-- CTAs -->
        <div class="reveal-5" style="display:flex;gap:1rem;flex-wrap:wrap;justify-content:flex-end;">
          <a href="#registration-link" class="btn-primary">Buy Driver Entry</a>
          <a href="#registration-link" class="btn-outline">Buy Spectator Tickets</a>
        </div>

      </div>
    </div>

    <!-- Right: photo -->
    <div class="hero-img-wrap">
      <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/festival-of-speed-hero.jpg" alt="Festival of Speed action shot">
    </div>

  </section>


  <!-- ═══════════════════════════════════════════
       SPONSORS
  ═══════════════════════════════════════════ -->
  <section style="background:#ffffff;padding:4rem 1.5rem;">
    <div style="max-width:1100px;margin:0 auto;">
      <p style="font-family:'Barlow Condensed',sans-serif;font-weight:600;letter-spacing:0.3em;font-size:1rem;background:var(--grad);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;text-transform:uppercase;text-align:center;margin-bottom:2.5rem;">
        Presented By Our Sponsors
      </p>
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:1rem;">
        <?php
        $sponsors = ['CB', 'KBD', 'SharkFest', 'Partner 04', 'Partner 05', 'Partner 06', 'Partner 07', 'Partner 08', 'Partner 09', 'Partner 10', 'Partner 11', 'Partner 12'];
        foreach ($sponsors as $s) : ?>
          <div class="sponsor-box" style="display:flex;align-items:center;justify-content:center;padding:1.5rem 1rem;background:#fafafa;">
            <span style="font-family:'Bebas Neue',sans-serif;font-size:1.4rem;color:#555;letter-spacing:0.1em;"><?php echo esc_html($s); ?></span>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>


  <!-- ═══════════════════════════════════════════
       DRIVER ENTRY PERKS
  ═══════════════════════════════════════════ -->
  <section class="bg-blush" style="padding:5rem 1.5rem 6rem;position:relative;overflow:hidden;">
    <div style="max-width:1100px;margin:0 auto;">

      <div style="margin-bottom:3rem;">
        <div class="accent-bar"></div>
        <h2 style="font-family:'Bebas Neue',sans-serif;font-size:clamp(2.5rem,6vw,4.5rem);color:#111;margin:0 0 0.5rem;line-height:1;">
          Driver Entry Includes
        </h2>
        <p style="font-family:'Barlow Condensed',sans-serif;font-weight:400;font-size:1.3rem;color:rgba(0,0,0,0.5);">Everything you need for three days on track.</p>
      </div>

      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1.25rem;">
        <?php
        $perks = [
          ['title' => '3-Day Track Access', 'desc' => 'Full access to on-track sessions across all three event days.'],
          ['title' => 'Paved Pits',         'desc' => 'Dedicated paved pit space with access to all track amenities.'],
          ['title' => 'Driver Parties',     'desc' => 'Exclusive Friday & Saturday night driver parties included.'],
          ['title' => 'Free Camping',       'desc' => 'Camp on-site all weekend — no reservation needed.'],
          ['title' => 'Showers On-Site',    'desc' => 'Clean shower facilities available throughout the event.'],
          ['title' => 'Fuel Available',     'desc' => 'On-site fuel so you spend less time sourcing and more time driving.'],
        ];
        foreach ($perks as $i => $perk) : ?>
          <div class="perk-card" style="padding:1.75rem 1.5rem;position:relative;overflow:hidden;">
            <span class="step-num"><?php echo str_pad($i + 1, 2, '0', STR_PAD_LEFT); ?></span>
            <div style="width:8px;height:8px;background:var(--grad);margin-bottom:1rem;transform:rotate(45deg);background:linear-gradient(135deg,#e8197d,#f97316);"></div>
            <h3 style="font-family:'Bebas Neue',sans-serif;font-size:1.5rem;color:#111;margin:0 0 0.5rem;letter-spacing:0.05em;"><?php echo esc_html($perk['title']); ?></h3>
            <p style="font-family:'Barlow Condensed',sans-serif;font-weight:400;font-size:1.2rem;color:rgba(0,0,0,0.5);margin:0;line-height:1.5;"><?php echo esc_html($perk['desc']); ?></p>
          </div>
        <?php endforeach; ?>
      </div>

      <div style="margin-top:2.5rem;">
        <a href="#registration-link" class="btn-primary">Register as a Driver</a>
      </div>

    </div>
  </section>


  <!-- ═══════════════════════════════════════════
       EVENT SCHEDULE
  ═══════════════════════════════════════════ -->
  <section style="background:#f5f5f5;padding:5rem 1.5rem;">
    <div style="max-width:1100px;margin:0 auto;">

      <div style="margin-bottom:3rem;">
        <div class="accent-bar"></div>
        <h2 style="font-family:'Bebas Neue',sans-serif;font-size:clamp(2.5rem,6vw,4.5rem);color:#111;margin:0;line-height:1;">
          Event Schedule
        </h2>
      </div>

      <?php
      $days = [
        [
          'day'  => 'Friday',
          'date' => 'May 23',
          'sessions' => [
            ['time' => '8:00 AM',  'name' => 'Gates Open / Registration'],
            ['time' => '9:00 AM',  'name' => 'Tech Inspection'],
            ['time' => '10:00 AM', 'name' => 'Hot Laps — Open Practice'],
            ['time' => '12:00 PM', 'name' => 'Lunch Break'],
            ['time' => '1:00 PM',  'name' => 'Hot Laps — Afternoon Session'],
            ['time' => '4:00 PM',  'name' => 'Track Closes'],
            ['time' => '7:00 PM',  'name' => 'Driver Welcome Party'],
          ],
        ],
        [
          'day'  => 'Saturday',
          'date' => 'May 24',
          'sessions' => [
            ['time' => '8:00 AM',  'name' => 'Gates Open'],
            ['time' => '9:00 AM',  'name' => 'Hot Laps — Morning Session'],
            ['time' => '11:00 AM', 'name' => 'Competition Runs Begin'],
            ['time' => '12:30 PM', 'name' => 'Lunch Break'],
            ['time' => '1:30 PM',  'name' => 'Competition Continues'],
            ['time' => '3:30 PM',  'name' => 'Tandem Practice'],
            ['time' => '5:00 PM',  'name' => 'Media Sessions'],
            ['time' => '7:00 PM',  'name' => 'Driver Party'],
          ],
        ],
        [
          'day'  => 'Sunday',
          'date' => 'May 25',
          'sessions' => [
            ['time' => '8:00 AM',  'name' => 'Gates Open'],
            ['time' => '9:00 AM',  'name' => 'Final Competition Runs'],
            ['time' => '11:30 AM', 'name' => 'Tandem Finals'],
            ['time' => '1:00 PM',  'name' => 'Lunch Break'],
            ['time' => '2:00 PM',  'name' => 'Awards Ceremony'],
            ['time' => '3:30 PM',  'name' => 'Track Closes / Load Out'],
          ],
        ],
      ];
      foreach ($days as $idx => $day) : ?>
        <div style="border-top:1px solid rgba(0,0,0,0.1);margin-bottom:0;">
          <button
            class="accordion-trigger"
            onclick="toggleAccordion(this)"
            style="width:100%;text-align:left;background:transparent;border:none;padding:1.5rem 0;cursor:pointer;display:flex;align-items:center;justify-content:space-between;gap:1rem;"
          >
            <div style="display:flex;align-items:baseline;gap:1.25rem;">
              <span style="font-family:'Bebas Neue',sans-serif;font-size:2rem;color:#111;letter-spacing:0.05em;"><?php echo esc_html($day['day']); ?></span>
              <span style="font-family:'Barlow Condensed',sans-serif;font-weight:500;font-size:1.2rem;background:var(--grad);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;letter-spacing:0.1em;text-transform:uppercase;"><?php echo esc_html($day['date']); ?></span>
            </div>
            <span class="accordion-chevron" style="font-size:1.5rem;line-height:1;background:var(--grad);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">&#8964;</span>
          </button>
          <div class="accordion-content<?php echo $idx === 0 ? ' open' : ''; ?>">
            <div style="padding:0 0 1.5rem;">
              <?php foreach ($day['sessions'] as $session) : ?>
                <div style="display:flex;align-items:baseline;gap:1.5rem;padding:0.6rem 0;border-bottom:1px solid rgba(0,0,0,0.06);">
                  <span style="font-family:'Barlow Condensed',sans-serif;font-weight:700;font-size:1.15rem;background:var(--grad);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;letter-spacing:0.05em;min-width:100px;"><?php echo esc_html($session['time']); ?></span>
                  <span style="font-family:'Barlow Condensed',sans-serif;font-weight:500;font-size:1.25rem;color:#333;"><?php echo esc_html($session['name']); ?></span>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
      <div style="border-top:1px solid rgba(0,0,0,0.1);"></div>

    </div>
  </section>


  <!-- ═══════════════════════════════════════════
       LODGING
  ═══════════════════════════════════════════ -->
  <section style="background:#ffffff;padding:5rem 1.5rem;">
    <div style="max-width:1100px;margin:0 auto;">

      <div style="margin-bottom:3rem;">
        <div class="accent-bar"></div>
        <h2 style="font-family:'Bebas Neue',sans-serif;font-size:clamp(2.5rem,6vw,4.5rem);color:#111;margin:0 0 0.5rem;line-height:1;">
          Partner Lodging
        </h2>
        <p style="font-family:'Barlow Condensed',sans-serif;font-weight:400;font-size:1.3rem;color:rgba(0,0,0,0.5);">Official hotel partners with reserved blocks for event attendees.</p>
      </div>

      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:1.5rem;">

        <div class="hotel-card" style="padding:2rem;">
          <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.5rem;">
            <div style="width:4px;height:40px;background:linear-gradient(180deg,#e8197d,#f97316);flex-shrink:0;"></div>
            <div>
              <h3 style="font-family:'Bebas Neue',sans-serif;font-size:1.75rem;color:#111;margin:0;letter-spacing:0.05em;">Hampton Inn</h3>
              <p style="font-family:'Barlow Condensed',sans-serif;font-weight:400;font-size:1.1rem;color:rgba(0,0,0,0.4);margin:0;">1658 Fillmore St., Twin Falls</p>
            </div>
          </div>
          <ul style="list-style:none;padding:0;margin:0 0 2rem;display:flex;flex-direction:column;gap:0.4rem;">
            <?php foreach (['Free breakfast included', 'Indoor pool & fitness center', 'Event rate available — ask at booking'] as $amenity) : ?>
              <li style="display:flex;align-items:center;gap:0.75rem;font-family:'Barlow Condensed',sans-serif;font-size:1.2rem;color:rgba(0,0,0,0.6);">
                <span style="background:var(--grad);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;font-size:0.7rem;">&#9632;</span> <?php echo esc_html($amenity); ?>
              </li>
            <?php endforeach; ?>
          </ul>
          <a href="#registration-link" class="btn-primary">Book Now</a>
        </div>

        <div class="hotel-card" style="padding:2rem;">
          <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.5rem;">
            <div style="width:4px;height:40px;background:linear-gradient(180deg,#e8197d,#f97316);flex-shrink:0;"></div>
            <div>
              <h3 style="font-family:'Bebas Neue',sans-serif;font-size:1.75rem;color:#111;margin:0;letter-spacing:0.05em;">Quality Inn</h3>
              <p style="font-family:'Barlow Condensed',sans-serif;font-weight:400;font-size:1.1rem;color:rgba(0,0,0,0.4);margin:0;">1910 Fillmore St., Twin Falls</p>
            </div>
          </div>
          <ul style="list-style:none;padding:0;margin:0 0 2rem;display:flex;flex-direction:column;gap:0.4rem;">
            <?php foreach (['Complimentary continental breakfast', 'Outdoor pool', 'Pet-friendly rooms available'] as $amenity) : ?>
              <li style="display:flex;align-items:center;gap:0.75rem;font-family:'Barlow Condensed',sans-serif;font-size:1.2rem;color:rgba(0,0,0,0.6);">
                <span style="background:var(--grad);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;font-size:0.7rem;">&#9632;</span> <?php echo esc_html($amenity); ?>
              </li>
            <?php endforeach; ?>
          </ul>
          <a href="#registration-link" class="btn-outline">Book Now</a>
        </div>

      </div>
    </div>
  </section>


  <!-- ═══════════════════════════════════════════
       TECH & RULES BANNER
  ═══════════════════════════════════════════ -->
  <section style="background:linear-gradient(135deg,#e8197d 0%,#f97316 100%);padding:4rem 1.5rem;position:relative;overflow:hidden;">
    <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;pointer-events:none;user-select:none;">
      <span style="font-family:'Bebas Neue',sans-serif;font-size:clamp(6rem,18vw,14rem);color:rgba(255,255,255,0.1);white-space:nowrap;line-height:1;">TECH &amp; RULES</span>
    </div>
    <div style="max-width:1100px;margin:0 auto;position:relative;display:flex;align-items:center;justify-content:space-between;gap:2rem;flex-wrap:wrap;">
      <div>
        <h2 style="font-family:'Bebas Neue',sans-serif;font-size:clamp(2rem,5vw,3.5rem);color:white;margin:0 0 0.5rem;line-height:1;letter-spacing:0.05em;">Official Tech &amp; Rules</h2>
        <p style="font-family:'Barlow Condensed',sans-serif;font-weight:400;font-size:1.3rem;color:rgba(255,255,255,0.85);margin:0;max-width:480px;">Review the full technical requirements and event rules before you register. All vehicles must pass tech inspection.</p>
      </div>
      <a href="#registration-link" style="background:white;color:#e8197d;font-family:'Barlow Condensed',sans-serif;font-size:1.15rem;font-weight:700;letter-spacing:0.15em;text-transform:uppercase;padding:1rem 2rem;display:inline-block;white-space:nowrap;transition:opacity 0.2s ease,transform 0.15s ease;clip-path:polygon(0 0,calc(100% - 10px) 0,100% 10px,100% 100%,10px 100%,0 calc(100% - 10px));"
         onmouseover="this.style.opacity='0.9';this.style.transform='translateY(-2px)'" onmouseout="this.style.opacity='1';this.style.transform='translateY(0)'">
        View Tech &amp; Rules
      </a>
    </div>
  </section>


  <!-- ═══════════════════════════════════════════
       BOTTOM CTA
  ═══════════════════════════════════════════ -->
  <section class="bg-blush" style="padding:5rem 1.5rem;text-align:center;">
    <div style="max-width:700px;margin:0 auto;">
      <p style="font-family:'Barlow Condensed',sans-serif;font-weight:600;letter-spacing:0.3em;font-size:1.05rem;background:var(--grad);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;text-transform:uppercase;margin-bottom:1rem;">Don't Miss It</p>
      <h2 style="font-family:'Bebas Neue',sans-serif;font-size:clamp(3rem,8vw,6rem);color:#111;margin:0 0 1rem;line-height:0.95;">Ready to Race?</h2>
      <p style="font-family:'Barlow Condensed',sans-serif;font-weight:400;font-size:1.4rem;color:rgba(0,0,0,0.5);margin:0 0 2.5rem;line-height:1.5;">Spots are limited — secure your entry before they're gone.</p>
      <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
        <a href="#registration-link" class="btn-primary">Buy Driver Entry</a>
        <a href="#registration-link" class="btn-outline">Buy Spectator Tickets</a>
      </div>
    </div>
  </section>

</div><!-- /.fos-page -->

<script>
function toggleAccordion(btn) {
  btn.classList.toggle('open');
  btn.nextElementSibling.classList.toggle('open');
}
</script>

<?php get_footer(); ?>
