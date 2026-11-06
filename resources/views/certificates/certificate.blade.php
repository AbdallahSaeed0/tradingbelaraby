<!doctype html><html lang="ar" dir="rtl"><head><meta charset="utf-8"><title>شهادة مشاركة - {{ $c['student_name_ar'] }} - أكاديمية تداول بالعربي</title><style>

{!! $fontFaces !!}
@page { size: 297mm 210mm; margin: 0; }
*{box-sizing:border-box;margin:0;padding:0}
html,body{width:297mm;background:#fff;font-family:'Cairo','DejaVu Sans',sans-serif;direction:rtl;
  -webkit-print-color-adjust:exact;print-color-adjust:exact}
.page{position:relative;width:297mm;height:210mm;overflow:hidden;page-break-after:always;break-after:page}
.page:last-child{page-break-after:auto;break-after:auto}

/* ---------- Certificate ---------- */
.cert{background:radial-gradient(ellipse at 50% 38%,#ffffff 0%,#fffaf0 52%,#f3e8cd 100%);color:#0b2a63}
.cert .candles{position:absolute;left:0;bottom:0;width:100%;height:46mm;opacity:0;display:none}
.cert .glow{position:absolute;inset:0;background:
  radial-gradient(circle at 10% 14%,rgba(217,162,41,.16),transparent 30%),
  radial-gradient(circle at 92% 88%,rgba(11,42,99,.07),transparent 32%)}
.frame-outer{position:absolute;inset:7mm;border:1.1mm solid #d9a229;border-radius:3mm}
.frame-inner{position:absolute;inset:11mm;border:.35mm solid #d9a229;border-radius:1.6mm}
.corner{position:absolute;width:15mm;height:15mm;border:0 solid #d9a229}
.c-tl{top:14mm;left:14mm;border-top-width:1.4mm;border-left-width:1.4mm}
.c-tr{top:14mm;right:14mm;border-top-width:1.4mm;border-right-width:1.4mm}
.c-bl{bottom:14mm;left:14mm;border-bottom-width:1.4mm;border-left-width:1.4mm}
.c-br{bottom:14mm;right:14mm;border-bottom-width:1.4mm;border-right-width:1.4mm}
.abs{position:absolute;left:0;right:0;text-align:center}
.brand-ar{top:17mm;font-weight:800;font-size:6.6mm;color:#0b2a63;line-height:1.2}
.brand-en{top:25.5mm;font-weight:700;font-size:3.1mm;letter-spacing:1.4mm;color:#b9861a;direction:ltr}
.title{top:33mm;font-weight:800;font-size:17mm;line-height:1.15;color:#0b2a63}
.title-en{top:52mm;font-weight:700;font-size:3.6mm;letter-spacing:2mm;color:#b9861a;direction:ltr}
.orn{top:60mm;display:flex;align-items:center;justify-content:center;gap:3mm}
.orn i{display:block;height:.35mm;width:42mm;background:linear-gradient(90deg,transparent,#d9a229)}
.orn i:last-child{background:linear-gradient(270deg,transparent,#d9a229)}
.orn b{display:block;width:2.4mm;height:2.4mm;background:#d9a229;transform:rotate(45deg)}
.lead{top:66mm;font-size:4.4mm;color:#4a5b7a;font-weight:400}
.name{top:73mm;font-family:'Amiri','Cairo',serif;font-weight:700;font-size:15mm;line-height:1.3;color:#b9861a;padding:0 24mm;white-space:nowrap}
.name-line{position:absolute;top:92.5mm;left:62mm;right:62mm;height:.35mm;background:linear-gradient(90deg,transparent,#d9a229,transparent)}
.name-en{top:94mm;font-size:4.6mm;font-weight:700;letter-spacing:.6mm;color:#0b2a63;direction:ltr}
.lead2{top:104mm;font-size:4.4mm;color:#4a5b7a}
.course{top:110mm;font-weight:800;font-size:8.6mm;line-height:1.3;color:#0b2a63;padding:0 28mm}
.course-en{top:123mm;font-size:4.2mm;font-weight:400;color:#4a5b7a;direction:ltr;letter-spacing:.3mm;padding:0 28mm}
.meta{top:134mm;display:flex;justify-content:center;gap:0}
.meta div{padding:0 11mm;text-align:center;border-left:.3mm solid rgba(185,134,26,.55)}
.meta div:last-child{border-left:0}
.meta small{display:block;font-size:3.1mm;color:#b9861a;font-weight:700;letter-spacing:.2mm}
.meta span{display:block;font-size:4.4mm;font-weight:700;color:#0b2a63}
.sig{position:absolute;right:30mm;bottom:31mm;width:62mm;text-align:center}
.sig .scribble{height:13mm;display:flex;align-items:flex-end;justify-content:center;color:#4a5b7a;font-size:3mm;opacity:.0}
.sig .line{height:.35mm;background:#d9a229;margin-bottom:1.6mm}
.sig b{display:block;font-size:4.6mm;font-weight:800}
.sig small{display:block;font-size:3mm;color:#4a5b7a;margin-top:.4mm;direction:ltr}
.qr{position:absolute;left:24mm;bottom:28mm;width:46mm;text-align:center}
.qr .box{background:#fff;border:.35mm solid #d9a229;padding:2mm;border-radius:1.6mm;width:24mm;height:24mm;margin:0 auto}
.qr .box svg{width:100%;height:100%;display:block}
.qr small{display:block;font-size:2.7mm;color:#4a5b7a;margin-top:1.4mm;line-height:1.35}
.seal{position:absolute;left:50%;bottom:27mm;transform:translateX(-50%);width:27mm;height:27mm;border-radius:50%;
  background:radial-gradient(circle at 35% 30%,#ffe29a,#f5c04a 45%,#c28a14 100%);box-shadow:0 0 0 1mm rgba(245,192,74,.35);
  display:flex;flex-direction:column;align-items:center;justify-content:center;color:#0b2a63;text-align:center}
.seal::after{content:"";position:absolute;inset:1.6mm;border:.4mm solid rgba(11,42,99,.55);border-radius:50%}
.seal b{font-size:5.4mm;font-weight:800;line-height:1.05}
.seal small{font-size:2.4mm;font-weight:700;letter-spacing:.5mm;direction:ltr}
.foot{bottom:13.4mm;font-size:2.7mm;color:#6b7a96;padding:0 24mm;line-height:1.5}

/* ---------- Dynamic-content rules ---------- */
.name span{display:inline-block}
.name-en,.course-en{white-space:nowrap;overflow:hidden}
.course{display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;text-overflow:ellipsis}
/* ---------- Screen-only toolbar (browser view / print fallback) ---------- */
.toolbar{display:none}
@media screen{
  html,body{width:auto;background:#e9edf3}
  .sheet{width:297mm;margin:0 auto 10mm;box-shadow:0 6px 30px rgba(6,26,61,.18)}
  .toolbar{display:flex;gap:10px;justify-content:center;align-items:center;flex-wrap:wrap;padding:14px;font-family:'Cairo',sans-serif}
  .toolbar button,.toolbar a{font:700 15px 'Cairo',sans-serif;border:0;border-radius:8px;padding:9px 20px;cursor:pointer;text-decoration:none}
  .toolbar .primary{background:#0b2a63;color:#fff}
  .toolbar .ghost{background:#fff;color:#0b2a63;border:1px solid #cfd8e8}
  .toolbar small{width:100%;text-align:center;color:#4a5b7a;font-size:13px}
}
@media print{.toolbar{display:none!important}}
</style></head><body>
@if (!empty($showToolbar))
<div class="toolbar">
  <button type="button" class="primary" onclick="window.print()">تحميل / طباعة PDF</button>
  @if (!empty($backUrl))<a class="ghost" href="{{ $backUrl }}">رجوع</a>@endif
  <small>اختر «حفظ كملف PDF» من نافذة الطباعة، واجعل المقاس A4 أفقي والهوامش «بلا».</small>
</div>
@endif
<div class="sheet">
<div class="page cert">
  <div class="glow"></div>
  <div class="frame-outer"></div><div class="frame-inner"></div>
  <div class="corner c-tl"></div><div class="corner c-tr"></div><div class="corner c-bl"></div><div class="corner c-br"></div>

  <div class="abs brand-ar">أكاديمية تداول بالعربي</div>
  <div class="abs brand-en">TRADING BEL ARABY</div>
  <div class="abs title">شهادة مشاركة</div>
  <div class="abs title-en">CERTIFICATE OF PARTICIPATION</div>
  <div class="abs orn"><i></i><b></b><i></i></div>

  <div class="abs lead">تشهد أكاديمية تداول بالعربي بأن</div>
  <div class="abs name" id="student-name"><span>{{ $c['student_name_ar'] }}</span></div>
  <div class="name-line"></div>
  @if (!empty($c['student_name_en']))
  <div class="abs name-en" id="student-name-en">{{ $c['student_name_en'] }}</div>
  @endif

  <div class="abs lead2">قد شارك في</div>
  <div class="abs course" id="course-title">{{ $c['course_title_ar'] }}</div>
  @if (!empty($c['course_title_en']))
  <div class="abs course-en" id="course-title-en">{{ $c['course_title_en'] }}</div>
  @endif

  <div class="abs meta">
    <div><small>تاريخ الإصدار</small><span dir="ltr">{{ $c['issue_date'] }}</span></div>
    @if (!empty($c['hours']))
    <div><small>مدة الدورة</small><span>{{ $c['hours'] }}</span></div>
    @endif
    <div><small>رقم الشهادة</small><span dir="ltr">{{ $c['certificate_id'] }}</span></div>
  </div>

  <div class="sig">
    <div class="scribble">&nbsp;</div>
    <div class="line"></div>
    <b>{{ $c['instructor_name'] }}</b>
    @if (!empty($c['instructor_title']))<small>{{ $c['instructor_title'] }}</small>@endif
  </div>

  <div class="seal"><b>TBA</b><small>ACADEMY</small></div>

  <div class="qr">
    <div class="box">{!! $qrSvg !!}</div>
    <small>للتحقق من الشهادة امسح الرمز<br/>أو زر الرابط المطبوع في الصفحة</small>
  </div>

  <div class="abs foot">شهادة مشاركة تعليمية صادرة عن أكاديمية تداول بالعربي، وليست اعتمادًا مهنيًا ولا ترخيصًا بمزاولة أي نشاط مالي. المحتوى لأغراض تعليمية فقط وليس نصيحة استثمارية.<br/><span style="direction:ltr;unicode-bidi:embed">{{ $c['verify_url'] }}</span></div>
</div>
</div>
<script>
(function () {
  var MM = 96 / 25.4;
  function fit() {
    // Student name: one line, shrink from 15mm down to 9mm if wider than 160mm.
    var name = document.getElementById('student-name');
    if (name) {
      var span = name.firstElementChild, fs = 15 * MM;
      while (span.offsetWidth > 160 * MM && fs > 9 * MM) { fs -= 1; name.style.fontSize = fs + 'px'; }
    }
    // English name / English course title: one line, hidden entirely if it overflows.
    ['student-name-en', 'course-title-en'].forEach(function (id) {
      var el = document.getElementById(id);
      if (el && el.scrollWidth > el.clientWidth + 1) el.style.display = 'none';
    });
    // Arabic course title wraps to a 2nd line -> it occupies the English title's slot.
    var course = document.getElementById('course-title'), courseEn = document.getElementById('course-title-en');
    if (course && courseEn) {
      var lh = parseFloat(getComputedStyle(course).lineHeight);
      if (course.offsetHeight > lh * 1.5) courseEn.style.display = 'none';
    }
    document.documentElement.setAttribute('data-ready', '1');
  }
  if (document.fonts && document.fonts.ready) document.fonts.ready.then(fit); else window.addEventListener('load', fit);
})();
</script>
</body></html>
