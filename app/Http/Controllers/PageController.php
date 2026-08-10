<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;

class PageController extends Controller
{
    /**
     * Static informational pages shown from the footer. Content is simple HTML
     * so the shop owner gets ready-made policy pages out of the box.
     */
    public function show(string $slug)
    {
        $s = SiteSetting::getAll();
        $name = $s['site_name'] ?? 'ODHIK SHOP';
        $phone = $s['phone'] ?? '';
        $email = $s['email'] ?? '';
        $address = $s['address'] ?? '';

        $pages = [
            'about' => [
                'title' => 'About Us',
                'body' => "<p><strong>{$name} E-commerce BD</strong> is a trusted online shopping platform in Bangladesh, offering genuine products at honest prices with fast home delivery and Cash on Delivery across the country.</p><p>Our mission is to make online shopping simple, safe and affordable for every customer. From electronics and gadgets to home, kitchen, fashion and baby care — we bring you a wide range of quality products, all in one place.</p><p>We are committed to 100% original products, secure checkout, and friendly customer support 7 days a week.</p>",
            ],
            'terms' => [
                'title' => 'Terms & Conditions',
                'body' => '<p>By using this website and placing an order, you agree to the following terms:</p><ul class="list-disc pl-5 space-y-1"><li>All prices are in Bangladeshi Taka (BDT) and inclusive of applicable charges unless stated otherwise.</li><li>Orders are confirmed after we verify your contact number.</li><li>Product images are for illustration; slight variations may occur.</li><li>We reserve the right to cancel any order in case of pricing errors or stock unavailability.</li><li>Delivery time may vary based on your location and product availability.</li></ul>',
            ],
            'privacy' => [
                'title' => 'Privacy Policy',
                'body' => '<p>Your privacy is important to us. We only collect the information needed to process and deliver your orders — such as your name, phone number and address.</p><ul class="list-disc pl-5 space-y-1"><li>We never sell or share your personal data with third parties for marketing.</li><li>Your information is used only for order processing, delivery and support.</li><li>We take reasonable measures to keep your data secure.</li></ul>',
            ],
            'refund-policy' => [
                'title' => 'Return & Refund Policy',
                'body' => '<p>We want you to shop with confidence.</p><ul class="list-disc pl-5 space-y-1"><li><strong>Easy 3-day return</strong> on eligible products if they are damaged, defective or incorrect.</li><li>To request a return, contact us within 3 days of receiving your order with your order number.</li><li>Refunds or exchanges are processed after the returned item is received and inspected.</li><li>Products must be unused and in original packaging.</li><li>Return shipping cost is shared 50/50 between the customer and ODHIK SHOP.</li></ul>',
            ],
            'how-to-order' => [
                'title' => 'How to Order',
                'body' => '<ol class="list-decimal pl-5 space-y-1"><li>Browse products and open the one you like.</li><li>Click <strong>Order Now</strong> for instant checkout, or <strong>Add to Cart</strong> to buy multiple items.</li><li>Fill in your name, mobile number and delivery address.</li><li>Choose your delivery area and payment method (Cash on Delivery available).</li><li>Click <strong>Place Order</strong> — we will call you to confirm!</li></ol>',
            ],
            'shipping' => [
                'title' => 'Shipping & Delivery',
                'body' => '<p>We deliver all over Bangladesh.</p><ul class="list-disc pl-5 space-y-1"><li><strong>Inside Dhaka:</strong> usually 1–2 working days.</li><li><strong>Outside Dhaka:</strong> usually 2–3 working days.</li><li>Delivery charges are shown at checkout based on your area.</li><li><strong>Cash on Delivery</strong> is available nationwide.</li></ul>',
            ],
            'faq' => [
                'title' => 'সচরাচর জিজ্ঞাসিত প্রশ্ন (FAQ)',
                'body' => self::faqBody(),
            ],
        ];

        abort_unless(isset($pages[$slug]), 404);

        $page = $pages[$slug];
        $page['contact'] = compact('phone', 'email', 'address');

        return view('page', ['page' => $page]);
    }

    private static function faqBody(): string
    {
        $faqs = [
            ['ODHIK SHOP থেকে কীভাবে অর্ডার করবো?', 'আপনার পছন্দের পণ্য নির্বাচন করে <strong>"কার্টে যোগ করুন"</strong> বা <strong>"Order Now"</strong> বাটনে ক্লিক করুন। এরপর আপনার নাম, মোবাইল নম্বর, সম্পূর্ণ ঠিকানা এবং প্রয়োজনীয় তথ্য দিয়ে অর্ডার সম্পন্ন করুন।'],
            ['ODHIK SHOP কি সারা বাংলাদেশে ডেলিভারি করে?', 'হ্যাঁ। আমরা ঢাকাসহ বাংলাদেশের অধিকাংশ জেলায় বিশ্বস্ত কুরিয়ার সার্ভিসের মাধ্যমে পণ্য ডেলিভারি করে থাকি। যেমন — স্টেডফাস্ট ও পাঠাও কুরিয়ার।'],
            ['ডেলিভারি চার্জ কত?', 'ডেলিভারি চার্জ আপনার অবস্থান অনুযায়ী নির্ধারিত হয়। চেকআউট পেজে অর্ডার করার সময় সঠিক ডেলিভারি চার্জ দেখতে পারবেন। (৬০-১৩০ টাকা সর্বোচ্চ)।'],
            ['অর্ডার করতে কি অগ্রিম টাকা দিতে হবে?', 'বেশিরভাগ পণ্যের ক্ষেত্রে <strong>ক্যাশ অন ডেলিভারি (COD)</strong> সুবিধা রয়েছে। তবে কিছু বিশেষ পণ্য বা নির্দিষ্ট এলাকায় অগ্রিম পেমেন্ট প্রয়োজন হতে পারে। এছাড়াও আপনার পূর্বের ডেলিভারি Success Rate কম হলে অগ্রিম দিতে হবে।'],
            ['Advance Payment কিভাবে দিবো?', 'বিকাশ / নগদ / রকেট / ব্যাংক (একাউন্ট নম্বর অর্ডার কনফার্মেশন কলে জানানো হবে)।'],
            ['কোন কোন পেমেন্ট পদ্ধতি গ্রহণ করা হয়?', 'আমরা ক্যাশ অন ডেলিভারি, বিকাশ, নগদ, রকেট, ব্যাংক ট্রান্সফার এবং অন্যান্য উপলব্ধ অনলাইন পেমেন্ট পদ্ধতি গ্রহণ করি।'],
            ['অর্ডার করার পর কত দিনের মধ্যে ডেলিভারি হবে?', '<strong>ঢাকার মধ্যে:</strong> ১২-২৪ ঘন্টা বা ১ কার্যদিবস।<br><strong>ঢাকার বাইরে:</strong> ৪৮-৭২ ঘন্টা বা ২-৩ কার্যদিবস।<br>বিশেষ পরিস্থিতিতে ডেলিভারির সময় কিছুটা পরিবর্তিত হতে পারে।'],
            ['অর্ডার কীভাবে ট্র্যাক করবো?', 'অর্ডার নিশ্চিত হওয়ার পর এসএমএস, ফোন বা কুরিয়ার ট্র্যাকিং নম্বরের মাধ্যমে আপনার অর্ডারের বর্তমান অবস্থা জানতে পারবেন।'],
            ['অর্ডার পরিবর্তন বা বাতিল করা যাবে?', 'হ্যাঁ। অর্ডার শিপমেন্টের আগে পরিবর্তন বা বাতিল করা সম্ভব। দ্রুত আমাদের কাস্টমার সাপোর্টে যোগাযোগ করুন।'],
            ['ভুল বা ক্ষতিগ্রস্ত পণ্য পেলে কী করবো?', 'পণ্য গ্রহণের ২৪ ঘণ্টার মধ্যে আমাদের জানালে যাচাই সাপেক্ষে রিপ্লেসমেন্ট বা রিফান্ড এর ব্যবস্থা করা হবে।'],
            ['পণ্য রিটার্ন করার নিয়ম কী?', 'পণ্য ব্যবহৃত না হলে, অক্ষত অবস্থায় এবং মূল প্যাকেজিংসহ নির্ধারিত সময়ের মধ্যে (<strong>৩ দিন</strong>) রিটার্নের আবেদন করা যাবে। বিস্তারিত জানতে আমাদের রিটার্ন পলিসি দেখুন।'],
            ['রিফান্ড পেতে কত সময় লাগে?', 'রিফান্ড অনুমোদনের পর সাধারণত ৩-৭ কার্যদিবসের মধ্যে আপনার ব্যবহৃত পেমেন্ট মাধ্যমে অর্থ ফেরত পাঠানো হয়।'],
            ['রিটার্ন শিপিং চার্জ কে বহন করবে?', '৫০% কাস্টমার এবং ৫০% অধিকশপ বহন করবে।'],
            ['পণ্যের গ্যারান্টি বা ওয়ারেন্টি আছে কি?', 'যেসব পণ্যে গ্যারান্টি বা ওয়ারেন্টি প্রযোজ্য, সেগুলোর তথ্য সংশ্লিষ্ট পণ্যের পেজে উল্লেখ থাকে।'],
            ['ওয়েবসাইটে প্রদর্শিত ছবির সঙ্গে পণ্যের মিল থাকবে কি?', 'আমরা বাস্তব পণ্যের ছবি ব্যবহার করার চেষ্টা করি। তবে আলো, ক্যামেরা বা ডিভাইসের ডিসপ্লের কারণে রঙে সামান্য পার্থক্য হতে পারে।'],
            ['আমার ব্যক্তিগত তথ্য কি নিরাপদ?', 'হ্যাঁ। ODHIK SHOP আপনার ব্যক্তিগত তথ্য নিরাপদভাবে সংরক্ষণ করে এবং আপনার অনুমতি ছাড়া তৃতীয় পক্ষের কাছে শেয়ার করে না।'],
            ['কাস্টমার সাপোর্টে কীভাবে যোগাযোগ করবো?', 'আপনি আমাদের ফোন, WhatsApp, Facebook Page, Messenger অথবা ইমেইল এর মাধ্যমে যোগাযোগ করতে পারেন।'],
            ['ভাঙা বা ক্ষতিগ্রস্ত পণ্য পেলে কী করবো?', 'ডেলিভারি ম্যানের সামনে প্যাকেট খুলে চেক করুন, সাথে সাথেই রিটার্ন করুন। অথবা <strong>আনবক্সিং ভিডিও রেকর্ড করুন</strong> (স্পষ্ট প্রমাণসহ)।'],
            ['পণ্য নিরাপদ থাকবে — কিভাবে নিশ্চিত করবেন?', 'আমাদের নিরাপত্তা ব্যবস্থা:<ol class="list-decimal pl-5 mt-2 space-y-1"><li>বাবল র‍্যাপ প্রোটেকশন</li><li>শক্ত কার্টন বক্স</li><li>CCTV মনিটরিং</li><li>প্রতিটি পণ্যের ভিডিও সংরক্ষণ</li></ol>'],
        ];

        $html = '<div class="space-y-4">';
        foreach ($faqs as [$q, $a]) {
            $html .= '<div class="border border-line rounded-lg p-4 bg-canvas/40">'
                .'<p class="font-bold text-ink mb-1.5 flex gap-2"><span class="text-brand shrink-0">👉</span><span>'.$q.'</span></p>'
                .'<div class="text-sm text-ink/80 leading-relaxed pl-6">'.$a.'</div>'
                .'</div>';
        }
        $html .= '</div>';
        $html .= '<p class="mt-6 text-sm text-ink/70 text-center border-t border-line pt-4"><strong class="text-brand">ODHIK SHOP</strong> — নির্ভরযোগ্য অনলাইন শপিং, দ্রুত ডেলিভারি এবং সাশ্রয়ী মূল্যে মানসম্মত পণ্য।</p>';

        return $html;
    }
}
