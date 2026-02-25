<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Page;

class PagesSeeder extends Seeder
{
    public function run(): void
    {
        // لو بدك كل مرة يعيد نفس الصفحات بدون تكرار
        // نستخدم updateOrCreate على slug

        $pages = [
            [
                'slug' => 'about',
                'sort_order' => 10,
                'is_public' => true,
                'title_ar' => 'من نحن',
                'title_en' => 'About Us',
                'meta_title_ar' => 'من نحن | GazaSannad',
                'meta_title_en' => 'About | GazaSannad',
                'meta_description_ar' => 'تعرف على GazaSannad، رسالتنا، قيمنا، وكيف نعمل لدعم حملات موثوقة بشفافية.',
                'meta_description_en' => 'Learn about GazaSannad—our mission, values, and how we support trusted campaigns with transparency.',
                'content_ar' => $this->aboutAr(),
                'content_en' => $this->aboutEn(),
            ],
            [
                'slug' => 'privacy',
                'sort_order' => 20,
                'is_public' => true,
                'title_ar' => 'سياسة الخصوصية',
                'title_en' => 'Privacy Policy',
                'meta_title_ar' => 'سياسة الخصوصية | GazaSannad',
                'meta_title_en' => 'Privacy Policy | GazaSannad',
                'meta_description_ar' => 'كيف نجمع البيانات ونحميها ونستخدمها، وحقوقك كمستخدم على منصة GazaSannad.',
                'meta_description_en' => 'How we collect, protect, and use data, and your rights as a GazaSannad user.',
                'content_ar' => $this->privacyAr(),
                'content_en' => $this->privacyEn(),
            ],
            [
                'slug' => 'terms',
                'sort_order' => 30,
                'is_public' => true,
                'title_ar' => 'الشروط والأحكام',
                'title_en' => 'Terms & Conditions',
                'meta_title_ar' => 'الشروط والأحكام | GazaSannad',
                'meta_title_en' => 'Terms | GazaSannad',
                'meta_description_ar' => 'شروط استخدام المنصة، مسؤوليات المستخدم، والسياسات العامة للمدفوعات والتبرعات.',
                'meta_description_en' => 'Platform terms of use, user responsibilities, and general donation/payment policies.',
                'content_ar' => $this->termsAr(),
                'content_en' => $this->termsEn(),
            ],
            [
                'slug' => 'faq',
                'sort_order' => 40,
                'is_public' => true,
                'title_ar' => 'الأسئلة الشائعة',
                'title_en' => 'FAQ',
                'meta_title_ar' => 'الأسئلة الشائعة | GazaSannad',
                'meta_title_en' => 'FAQ | GazaSannad',
                'meta_description_ar' => 'إجابات واضحة على أكثر الأسئلة تكرارًا حول التبرع، الحملات، والتقارير.',
                'meta_description_en' => 'Clear answers to common questions about donations, campaigns, and reporting.',
                'content_ar' => $this->faqAr(),
                'content_en' => $this->faqEn(),
            ],
            [
                'slug' => 'transparency',
                'sort_order' => 50,
                'is_public' => true,
                'title_ar' => 'الشفافية وكيف نضمن الثقة',
                'title_en' => 'Transparency & Trust',
                'meta_title_ar' => 'الشفافية | GazaSannad',
                'meta_title_en' => 'Transparency | GazaSannad',
                'meta_description_ar' => 'مبادئ الشفافية، آلية المراجعة، وكيف نعرض التقارير لتمنحك ثقة كاملة.',
                'meta_description_en' => 'Transparency principles, review process, and how we publish reports to earn your trust.',
                'content_ar' => $this->transparencyAr(),
                'content_en' => $this->transparencyEn(),
            ],
            [
                'slug' => 'contact',
                'sort_order' => 60,
                'is_public' => true,
                'title_ar' => 'تواصل معنا',
                'title_en' => 'Contact Us',
                'meta_title_ar' => 'تواصل معنا | GazaSannad',
                'meta_title_en' => 'Contact | GazaSannad',
                'meta_description_ar' => 'طرق التواصل والدعم: البريد، الهاتف، واتساب، وأسئلة الشراكات.',
                'meta_description_en' => 'Support contacts: email, phone, WhatsApp, and partnership inquiries.',
                'content_ar' => $this->contactAr(),
                'content_en' => $this->contactEn(),
            ],
        ];

        foreach ($pages as $p) {
            Page::query()->updateOrCreate(
                ['slug' => $p['slug']],
                [
                    'title_ar' => $p['title_ar'],
                    'title_en' => $p['title_en'],
                    'content_ar' => $p['content_ar'],
                    'content_en' => $p['content_en'],
                    'meta_title_ar' => $p['meta_title_ar'],
                    'meta_title_en' => $p['meta_title_en'],
                    'meta_description_ar' => $p['meta_description_ar'],
                    'meta_description_en' => $p['meta_description_en'],
                    'is_public' => $p['is_public'],
                    'sort_order' => $p['sort_order'],
                ]
            );
        }
    }

    // -------------------------
    // HTML CONTENT (AR / EN)
    // -------------------------

    private function aboutAr(): string
    {
        return <<<HTML
<section>
  <p><strong>GazaSannad</strong> منصة لدعم الحملات والتبرعات بطريقة واضحة ومحترمة، هدفها تمكين المتبرع من اتخاذ قرار واعٍ عبر معلومات دقيقة، وعرض منظم، وتجربة تبرع سهلة.</p>

  <div class="mt-6 p-4 rounded-2xl border border-slate-200 bg-slate-50">
    <p class="m-0"><strong>مهم:</strong> نُولي ثقة المتبرع أعلى أولوية. لذلك نركز على التوثيق، وإدارة المحتوى، وإظهار التقارير والمتابعة.</p>
  </div>

  <h2 class="mt-10">رسالتنا</h2>
  <p>نؤمن أن التبرع ليس “عملية دفع” فقط، بل علاقة ثقة طويلة. رسالتنا هي بناء تجربة تبرع: <strong>شفافة</strong>، <strong>إنسانية</strong>، و<strong>قابلة للتحقق</strong>.</p>

  <h2 class="mt-10">قيمنا الأساسية</h2>
  <ul>
    <li><strong>الشفافية:</strong> عرض المعلومات بوضوح وإتاحة تقارير وتحديثات.</li>
    <li><strong>الموثوقية:</strong> تنظيم المحتوى، والتحقق من البيانات قدر الإمكان قبل النشر.</li>
    <li><strong>الخصوصية:</strong> احترام بيانات المتبرع وعدم مشاركتها دون داعٍ.</li>
    <li><strong>سهولة الاستخدام:</strong> تجربة تبرع بسيطة بدون تعقيد.</li>
  </ul>

  <h2 class="mt-10">كيف نعمل</h2>
  <ol>
    <li>نُنشئ حملات بمعلومات واضحة (الهدف، التفاصيل، التحديثات، والتقارير).</li>
    <li>نعرض التبرعات بشكل منظم مع احترام الخصوصية (إمكانية إخفاء الاسم).</li>
    <li>نُظهر التقارير والتحديثات لتعزيز الاطلاع والمتابعة.</li>
  </ol>

  <h2 class="mt-10">لماذا GazaSannad؟</h2>
  <p>لأننا نبني منصة تركز على <strong>الثقة</strong> كمنتج: واجهة نظيفة، معلومات كاملة، وتحديثات وتوثيق.</p>

  <div class="mt-10 grid md:grid-cols-3 gap-4">
    <div class="p-4 rounded-2xl border border-slate-200">
      <div class="font-bold">تقارير</div>
      <div class="text-sm text-slate-600 mt-1">نشر تقارير منظمة تساعدك على المتابعة.</div>
    </div>
    <div class="p-4 rounded-2xl border border-slate-200">
      <div class="font-bold">تحديثات</div>
      <div class="text-sm text-slate-600 mt-1">تحديثات الحملة تُبقيك على اطلاع.</div>
    </div>
    <div class="p-4 rounded-2xl border border-slate-200">
      <div class="font-bold">خصوصية</div>
      <div class="text-sm text-slate-600 mt-1">التحكم بظهور الاسم، وحماية البيانات.</div>
    </div>
  </div>

  <p class="mt-10 text-sm text-slate-500">آخر تحديث: <strong>2026-02-21</strong></p>
</section>
HTML;
    }

    private function aboutEn(): string
    {
        return <<<HTML
<section>
  <p><strong>GazaSannad</strong> is a campaigns & donations platform built around clarity, respect, and trust. Our goal is to help donors make confident decisions through structured information and a smooth giving experience.</p>

  <div class="mt-6 p-4 rounded-2xl border border-slate-200 bg-slate-50">
    <p class="m-0"><strong>Note:</strong> Donor trust is our top priority. We focus on documentation, structured content, updates, and reporting.</p>
  </div>

  <h2 class="mt-10">Our Mission</h2>
  <p>We believe donating is not just a payment—it’s a long-term trust relationship. Our mission is to build a giving experience that is <strong>transparent</strong>, <strong>human</strong>, and <strong>verifiable</strong>.</p>

  <h2 class="mt-10">Core Values</h2>
  <ul>
    <li><strong>Transparency:</strong> clear information and accessible updates & reports.</li>
    <li><strong>Reliability:</strong> structured content and best-effort verification.</li>
    <li><strong>Privacy:</strong> respecting donor data and minimizing exposure.</li>
    <li><strong>Simplicity:</strong> easy-to-use flows without unnecessary friction.</li>
  </ul>

  <h2 class="mt-10">How It Works</h2>
  <ol>
    <li>We publish campaigns with clear goals, details, updates, and reports.</li>
    <li>Donations are presented in an organized way with privacy options (anonymous giving).</li>
    <li>Reports and updates keep supporters informed and confident.</li>
  </ol>

  <h2 class="mt-10">Why GazaSannad?</h2>
  <p>Because we treat <strong>trust</strong> as a product: clean UI, complete information, and consistent updates.</p>

  <div class="mt-10 grid md:grid-cols-3 gap-4">
    <div class="p-4 rounded-2xl border border-slate-200">
      <div class="font-bold">Reports</div>
      <div class="text-sm text-slate-600 mt-1">Organized reporting for clear follow-up.</div>
    </div>
    <div class="p-4 rounded-2xl border border-slate-200">
      <div class="font-bold">Updates</div>
      <div class="text-sm text-slate-600 mt-1">Campaign updates keep you in the loop.</div>
    </div>
    <div class="p-4 rounded-2xl border border-slate-200">
      <div class="font-bold">Privacy</div>
      <div class="text-sm text-slate-600 mt-1">Anonymous options and data protection.</div>
    </div>
  </div>

  <p class="mt-10 text-sm text-slate-500">Last updated: <strong>2026-02-21</strong></p>
</section>
HTML;
    }

    private function privacyAr(): string
    {
        return <<<HTML
<section>
  <p>نحترم خصوصيتك ونلتزم بحماية بياناتك. تشرح هذه السياسة ما نجمعه من بيانات وكيف نستخدمه وكيف يمكنك التحكم به.</p>

  <h2 class="mt-10">ما البيانات التي نجمعها؟</h2>
  <ul>
    <li><strong>بيانات الحساب:</strong> الاسم، البريد الإلكتروني، وتفاصيل الدخول.</li>
    <li><strong>بيانات التبرع:</strong> المبلغ، العملة، حالة العملية، ومرجع الدفع (إن وجد).</li>
    <li><strong>بيانات اختيارية:</strong> اسم المتبرع/بريد المتبرع إن أدخلها.</li>
  </ul>

  <h2 class="mt-10">كيف نستخدم البيانات؟</h2>
  <ul>
    <li>لتشغيل المنصة وإتمام العمليات وإدارة الحملات والتقارير.</li>
    <li>للتحسين ومنع الاحتيال وحماية النظام.</li>
    <li>للتواصل عند الحاجة (مثل دعم فني أو توضيح معاملة).</li>
  </ul>

  <h2 class="mt-10">الخصوصية والظهور للعلن</h2>
  <p>يمكن للمتبرع اختيار <strong>إخفاء الاسم</strong>. لا نعرض معلومات حساسة في الواجهات العامة.</p>

  <h2 class="mt-10">الأمان</h2>
  <ul>
    <li>تخزين منظم للبيانات في قاعدة بيانات.</li>
    <li>تقييد الوصول داخل لوحة الإدارة وفق صلاحيات.</li>
    <li>سجلات وتحديثات محتوى تساعد على التدقيق والمتابعة.</li>
  </ul>

  <h2 class="mt-10">حقوقك</h2>
  <ul>
    <li>طلب تصحيح بياناتك.</li>
    <li>طلب حذف حسابك (مع مراعاة السجلات المحاسبية/التشغيلية إن لزم).</li>
    <li>الاستفسار عن أي معالجة بيانات.</li>
  </ul>

  <h2 class="mt-10">التواصل</h2>
  <p>لأي استفسار حول الخصوصية، راسلنا عبر البريد الموجود في صفحة “تواصل معنا”.</p>

  <p class="mt-10 text-sm text-slate-500">آخر تحديث: <strong>2026-02-21</strong></p>
</section>
HTML;
    }

    private function privacyEn(): string
    {
        return <<<HTML
<section>
  <p>We respect your privacy and are committed to protecting your data. This policy explains what we collect, how we use it, and how you can control it.</p>

  <h2 class="mt-10">What data do we collect?</h2>
  <ul>
    <li><strong>Account data:</strong> name, email, and login details.</li>
    <li><strong>Donation data:</strong> amount, currency, status, and payment reference (if any).</li>
    <li><strong>Optional data:</strong> donor name/email if provided.</li>
  </ul>

  <h2 class="mt-10">How do we use data?</h2>
  <ul>
    <li>To operate the platform and manage campaigns, updates, and reports.</li>
    <li>To improve services, prevent fraud, and secure the system.</li>
    <li>To contact you when needed (support or transaction clarification).</li>
  </ul>

  <h2 class="mt-10">Public visibility</h2>
  <p>Donors can choose <strong>anonymous giving</strong>. We do not display sensitive information publicly.</p>

  <h2 class="mt-10">Security</h2>
  <ul>
    <li>Structured storage in the database.</li>
    <li>Role/permission-based access inside the admin panel.</li>
    <li>Logs and updates help with review and accountability.</li>
  </ul>

  <h2 class="mt-10">Your rights</h2>
  <ul>
    <li>Request correction of your data.</li>
    <li>Request account deletion (subject to legal/accounting requirements if applicable).</li>
    <li>Ask about any data processing.</li>
  </ul>

  <h2 class="mt-10">Contact</h2>
  <p>For privacy questions, contact us via the email listed on the “Contact” page.</p>

  <p class="mt-10 text-sm text-slate-500">Last updated: <strong>2026-02-21</strong></p>
</section>
HTML;
    }

    private function termsAr(): string
    {
        return <<<HTML
<section>
  <p>باستخدامك لمنصة <strong>GazaSannad</strong> فإنك توافق على هذه الشروط. هدفنا توفير تجربة آمنة وواضحة للجميع.</p>

  <h2 class="mt-10">تعريفات</h2>
  <ul>
    <li><strong>المنصة:</strong> GazaSannad.</li>
    <li><strong>المستخدم:</strong> أي شخص يتصفح أو يستخدم الخدمات.</li>
    <li><strong>المتبرع:</strong> مستخدم يقوم بإجراء تبرع.</li>
    <li><strong>الحملة:</strong> صفحة تجمع بيانات هدف وتفاصيل وتحديثات.</li>
  </ul>

  <h2 class="mt-10">استخدام المنصة</h2>
  <ul>
    <li>الالتزام بعدم إساءة استخدام النظام أو محاولة اختراقه.</li>
    <li>الامتناع عن نشر محتوى مضلل أو مخالف.</li>
    <li>احترام خصوصية الآخرين وعدم جمع بياناتهم.</li>
  </ul>

  <h2 class="mt-10">التبرعات والمدفوعات</h2>
  <ul>
    <li>قد تختلف طرق الدفع حسب الإعدادات والبوابة المستخدمة.</li>
    <li>حالة العملية (pending/paid/failed) تُعرض وفق بيانات النظام/بوابة الدفع.</li>
    <li>في حال وجود خطأ، يمكن التواصل مع الدعم لتدقيق الحالة.</li>
  </ul>

  <h2 class="mt-10">الشفافية والمحتوى</h2>
  <p>نسعى لعرض المعلومات بوضوح. قد تُنشر تقارير وتحديثات للحملات وفق سياسات النشر داخل لوحة التحكم.</p>

  <h2 class="mt-10">إخلاء مسؤولية</h2>
  <ul>
    <li>المنصة تقدم إطارًا تنظيميًا وعرضًا للمحتوى، ولا تضمن نتائج أي حملة خارج ما هو موثق.</li>
    <li>نلتزم ببذل أفضل جهد للتأكد من وضوح البيانات وتحديثها.</li>
  </ul>

  <h2 class="mt-10">تعديلات الشروط</h2>
  <p>قد نقوم بتحديث هذه الشروط لتحسين الخدمة. سيتم نشر آخر تحديث في أسفل الصفحة.</p>

  <p class="mt-10 text-sm text-slate-500">آخر تحديث: <strong>2026-02-21</strong></p>
</section>
HTML;
    }

    private function termsEn(): string
    {
        return <<<HTML
<section>
  <p>By using <strong>GazaSannad</strong>, you agree to these terms. Our goal is to provide a safe and clear experience for everyone.</p>

  <h2 class="mt-10">Definitions</h2>
  <ul>
    <li><strong>Platform:</strong> GazaSannad.</li>
    <li><strong>User:</strong> anyone who browses or uses the services.</li>
    <li><strong>Donor:</strong> a user who makes a donation.</li>
    <li><strong>Campaign:</strong> a page with a goal, details, and updates.</li>
  </ul>

  <h2 class="mt-10">Platform usage</h2>
  <ul>
    <li>Do not abuse the system or attempt unauthorized access.</li>
    <li>Do not publish misleading or prohibited content.</li>
    <li>Respect privacy; do not collect other users’ data.</li>
  </ul>

  <h2 class="mt-10">Donations & Payments</h2>
  <ul>
    <li>Payment methods may vary based on configuration and payment gateway.</li>
    <li>Transaction status (pending/paid/failed) is displayed based on system/gateway signals.</li>
    <li>If something seems wrong, contact support for verification.</li>
  </ul>

  <h2 class="mt-10">Transparency & Content</h2>
  <p>We aim for clarity. Reports and updates may be published according to admin publishing policies.</p>

  <h2 class="mt-10">Disclaimer</h2>
  <ul>
    <li>The platform provides structure and presentation; it does not guarantee outcomes beyond what is documented.</li>
    <li>We make best efforts to keep data clear and updated.</li>
  </ul>

  <h2 class="mt-10">Changes to these terms</h2>
  <p>We may update these terms to improve service. The latest update date will be shown below.</p>

  <p class="mt-10 text-sm text-slate-500">Last updated: <strong>2026-02-21</strong></p>
</section>
HTML;
    }

    private function faqAr(): string
    {
        return <<<HTML
<section>
  <p>هنا تجد إجابات سريعة وواضحة. إذا لم تجد جوابًا، تواصل معنا وسنساعدك.</p>

  <h2 class="mt-10">أسئلة شائعة</h2>

  <h3>كيف أتأكد أن الحملة موثوقة؟</h3>
  <p>نوصي بقراءة تفاصيل الحملة، متابعة التحديثات، والاطلاع على التقارير عند توفرها. نحن نعرض المعلومات بشكل منظم لتسهيل التحقق.</p>

  <h3>هل يمكنني التبرع بدون إظهار اسمي؟</h3>
  <p>نعم، يمكنك اختيار خيار <strong>التبرع بشكل مجهول</strong> أثناء إدخال بيانات التبرع.</p>

  <h3>هل أستطيع استلام إيصال أو إثبات؟</h3>
  <p>يمكن توفير بيانات المعاملة وحالتها داخل النظام، وتطوير الإيصالات يتم بحسب إعدادات النظام/بوابة الدفع.</p>

  <h3>متى تظهر التبرعات على الحملة؟</h3>
  <p>يعتمد ذلك على حالة العملية. عند نجاح الدفع تتحول الحالة إلى <strong>paid</strong> وتظهر في التقارير الداخلية/العامة حسب إعدادات العرض.</p>

  <h3>كيف أتواصل مع الدعم؟</h3>
  <p>عبر البريد أو الهاتف أو واتساب من صفحة “تواصل معنا”.</p>

  <div class="mt-8 p-4 rounded-2xl border border-slate-200 bg-slate-50">
    <p class="m-0"><strong>نصيحة:</strong> أفضل طريقة لرفع الثقة هي قراءة “الشفافية وكيف نضمن الثقة” والاطلاع على التقارير.</p>
  </div>

  <p class="mt-10 text-sm text-slate-500">آخر تحديث: <strong>2026-02-21</strong></p>
</section>
HTML;
    }

    private function faqEn(): string
    {
        return <<<HTML
<section>
  <p>Here are quick, clear answers. If you don’t find what you need, contact us and we’ll help.</p>

  <h2 class="mt-10">Frequently Asked Questions</h2>

  <h3>How do I know a campaign is trustworthy?</h3>
  <p>Read campaign details, follow updates, and review reports when available. We structure information to make verification easier.</p>

  <h3>Can I donate anonymously?</h3>
  <p>Yes. Choose <strong>anonymous donation</strong> during the donation flow.</p>

  <h3>Can I receive a receipt or proof?</h3>
  <p>Transaction details and status are available in the system. Receipts depend on gateway/configuration and can be expanded as needed.</p>

  <h3>When do donations appear on a campaign?</h3>
  <p>It depends on transaction status. Once successful, status becomes <strong>paid</strong> and appears based on visibility settings.</p>

  <h3>How do I contact support?</h3>
  <p>Use email/phone/WhatsApp via the “Contact” page.</p>

  <div class="mt-8 p-4 rounded-2xl border border-slate-200 bg-slate-50">
    <p class="m-0"><strong>Tip:</strong> The best way to build confidence is to read “Transparency & Trust” and review reports.</p>
  </div>

  <p class="mt-10 text-sm text-slate-500">Last updated: <strong>2026-02-21</strong></p>
</section>
HTML;
    }

    private function transparencyAr(): string
    {
        return <<<HTML
<section>
  <p>نعتبر الشفافية معيارًا أساسيًا لبناء الثقة مع المتبرعين. لذلك نقدم آليات واضحة لمتابعة الحملات وتحديثاتها.</p>

  <h2 class="mt-10">مبادئ الشفافية لدينا</h2>
  <ul>
    <li><strong>معلومات واضحة:</strong> الهدف، الحالة، والمدة الزمنية للحملة.</li>
    <li><strong>تحديثات منتظمة:</strong> نشر تحديثات توضح ماذا تم إنجازه.</li>
    <li><strong>تقارير عند الحاجة:</strong> مستندات وتقارير تدعم المتابعة والتقييم.</li>
    <li><strong>خصوصية المتبرع:</strong> عدم عرض بيانات حساسة، ودعم التبرع المجهول.</li>
  </ul>

  <h2 class="mt-10">كيف نعرض المعلومات؟</h2>
  <ol>
    <li>واجهة حملة مرتبة تسهل القراءة.</li>
    <li>قسم التحديثات لتوثيق الأحداث والتقدم.</li>
    <li>قسم التقارير (PDF) للتوثيق والإثبات.</li>
  </ol>

  <div class="mt-8 p-5 rounded-3xl border border-slate-200 bg-white">
    <h3 class="mt-0">مؤشرات تساعدك على اتخاذ قرار</h3>
    <ul>
      <li>وجود تقارير/تحديثات حديثة.</li>
      <li>وضوح الهدف والمحتوى.</li>
      <li>تناسق البيانات عبر الوقت.</li>
    </ul>
  </div>

  <p class="mt-10 text-sm text-slate-500">آخر تحديث: <strong>2026-02-21</strong></p>
</section>
HTML;
    }

    private function transparencyEn(): string
    {
        return <<<HTML
<section>
  <p>Transparency is the foundation of donor trust. We provide clear mechanisms to follow campaigns, updates, and reports.</p>

  <h2 class="mt-10">Our Transparency Principles</h2>
  <ul>
    <li><strong>Clear information:</strong> goal, status, and timeline.</li>
    <li><strong>Regular updates:</strong> documented progress and milestones.</li>
    <li><strong>Reports when needed:</strong> documents that support review and verification.</li>
    <li><strong>Donor privacy:</strong> no sensitive data displayed; anonymous donations supported.</li>
  </ul>

  <h2 class="mt-10">How we present information</h2>
  <ol>
    <li>A structured campaign page for easy reading.</li>
    <li>An updates section to document progress.</li>
    <li>A reports section (PDF) for evidence and documentation.</li>
  </ol>

  <div class="mt-8 p-5 rounded-3xl border border-slate-200 bg-white">
    <h3 class="mt-0">Decision signals to look for</h3>
    <ul>
      <li>Recent reports/updates.</li>
      <li>Clear goals and content.</li>
      <li>Consistent information over time.</li>
    </ul>
  </div>

  <p class="mt-10 text-sm text-slate-500">Last updated: <strong>2026-02-21</strong></p>
</section>
HTML;
    }

    private function contactAr(): string
    {
        return <<<HTML
<section>
  <p>يسعدنا تواصلك معنا. إن كان لديك سؤال عن التبرع أو تقرير أو حملة، سنرد بأسرع وقت ممكن.</p>

  <div class="mt-8 grid md:grid-cols-3 gap-4">
    <div class="p-4 rounded-2xl border border-slate-200">
      <div class="font-bold">الدعم عبر البريد</div>
      <div class="text-sm text-slate-600 mt-1">راسلنا وسنساعدك في أي استفسار.</div>
      <div class="mt-3 text-sm"><strong>البريد:</strong> <span class="text-slate-700">gazasannad@gmail.com</span></div>
    </div>

    <div class="p-4 rounded-2xl border border-slate-200">
      <div class="font-bold">الهاتف</div>
      <div class="text-sm text-slate-600 mt-1">للاستفسارات العاجلة.</div>
      <div class="mt-3 text-sm"><strong>هاتف:</strong> <span class="text-slate-700">0599984799</span></div>
    </div>

    <div class="p-4 rounded-2xl border border-slate-200">
      <div class="font-bold">واتساب</div>
      <div class="text-sm text-slate-600 mt-1">تواصل سريع ومباشر.</div>
      <div class="mt-3 text-sm"><strong>WhatsApp:</strong> <span class="text-slate-700">0599984799</span></div>
    </div>
  </div>

  <h2 class="mt-10">شراكات ومبادرات</h2>
  <p>إن كنت تمثل جهة ترغب بالتعاون أو تقديم دعم منظم، يسعدنا التواصل وتبادل التفاصيل.</p>

  <p class="mt-10 text-sm text-slate-500">آخر تحديث: <strong>2026-02-21</strong></p>
</section>
HTML;
    }

    private function contactEn(): string
    {
        return <<<HTML
<section>
  <p>We’re happy to hear from you. If you have questions about donations, reports, or campaigns, we’ll respond as soon as possible.</p>

  <div class="mt-8 grid md:grid-cols-3 gap-4">
    <div class="p-4 rounded-2xl border border-slate-200">
      <div class="font-bold">Email Support</div>
      <div class="text-sm text-slate-600 mt-1">Send us a message and we’ll help.</div>
      <div class="mt-3 text-sm"><strong>Email:</strong> <span class="text-slate-700">gazasannad@gmail.com</span></div>
    </div>

    <div class="p-4 rounded-2xl border border-slate-200">
      <div class="font-bold">Phone</div>
      <div class="text-sm text-slate-600 mt-1">For urgent inquiries.</div>
      <div class="mt-3 text-sm"><strong>Phone:</strong> <span class="text-slate-700">0599984799</span></div>
    </div>

    <div class="p-4 rounded-2xl border border-slate-200">
      <div class="font-bold">WhatsApp</div>
      <div class="text-sm text-slate-600 mt-1">Quick and direct communication.</div>
      <div class="mt-3 text-sm"><strong>WhatsApp:</strong> <span class="text-slate-700">0599984799</span></div>
    </div>
  </div>

  <h2 class="mt-10">Partnerships</h2>
  <p>If you represent an organization and would like to collaborate or provide structured support, we’d love to connect.</p>

  <p class="mt-10 text-sm text-slate-500">Last updated: <strong>2026-02-21</strong></p>
</section>
HTML;
    }
}
