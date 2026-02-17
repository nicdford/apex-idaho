<?php
/**
 * Template Name: Font Size Test
 */
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Font Size Test</title>
  <style>
    body { font-family: sans-serif; padding: 2rem; }
    .result { margin-bottom: 1rem; padding: 1rem; background: #f5f5f5; }
  </style>
</head>
<body>

  <h1>Font Size Test Page</h1>
  <p>This page loads <strong>no theme styles</strong> — only this file.</p>

  <div class="result">
    <strong>Browser default (no override):</strong><br>
    <span id="html-size"></span>
  </div>

  <div class="result">
    <strong>1rem rendered size:</strong><br>
    <span style="font-size:1rem;">This text is 1rem. If base is 16px this should be 16px.</span>
    <span id="rem-size"></span>
  </div>

  <div class="result">
    <strong>1.5rem rendered size:</strong><br>
    <span style="font-size:1.5rem;">This text is 1.5rem. Should be 24px at 16px base.</span>
  </div>

  <div class="result">
    <strong>16px explicit:</strong><br>
    <span style="font-size:16px;">This text is hardcoded 16px.</span>
  </div>

  <div class="result">
    <strong>Computed html font-size:</strong> <span id="computed"></span>
  </div>

  <script>
    var htmlEl = document.documentElement;
    var computed = window.getComputedStyle(htmlEl).fontSize;
    document.getElementById('computed').textContent = computed;
    document.getElementById('html-size').textContent = 'html computed font-size: ' + computed;

    var remSpan = document.querySelector('[style="font-size:1rem;"]');
    if (remSpan) {
      var remComputed = window.getComputedStyle(remSpan).fontSize;
      document.getElementById('rem-size').textContent = ' → computed: ' + remComputed;
    }
  </script>

</body>
</html>
<?php die(); // prevent WP from loading anything else ?>
