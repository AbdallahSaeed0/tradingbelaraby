<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Blog;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapController extends Controller
{
    /**
     * Build the sitemap (shared between web route and artisan command).
     */
    public static function buildSitemap(): Sitemap
    {
        $sitemap = Sitemap::create()
            ->add(Url::create(url(route('home')))->setPriority(1)->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY))
            ->add(Url::create(url(route('courses.index')))->setPriority(0.9)->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY))
            ->add(Url::create(url(route('blog.index')))->setPriority(0.9)->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY))
            ->add(Url::create(url(route('categories.index')))->setPriority(0.8)->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY))
            ->add(Url::create(url(route('contact')))->setPriority(0.5)->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY))
            ->add(Url::create(url(route('about-us')))->setPriority(0.5)->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY))
            ->add(Url::create(url(route('academy-policy')))->setPriority(0.4)->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY))
            ->add(Url::create(url(route('instructor.index')))->setPriority(0.8)->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY));

        Course::published()->get()->each(function (Course $course) use ($sitemap) {
            $sitemap->add(
                Url::create(url(route('courses.show', $course)))
                    ->setLastModificationDate($course->updated_at)
                    ->setPriority(0.8)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
            );
        });

        Blog::published()->get()->each(function (Blog $blog) use ($sitemap) {
            $sitemap->add(
                Url::create(url(route('blog.show', $blog)))
                    ->setLastModificationDate($blog->updated_at)
                    ->setPriority(0.7)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
            );
        });

        Admin::whereHas('courses', fn ($q) => $q->where('status', '!=', 'draft'))
            ->get()
            ->each(function (Admin $admin) use ($sitemap) {
                $sitemap->add(
                    Url::create(url(route('instructor.show', $admin->id)))
                        ->setLastModificationDate($admin->updated_at)
                        ->setPriority(0.6)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                );
            });

        return $sitemap;
    }

    /**
     * Serve sitemap.xml (courses, blog posts, instructors, static pages).
     */
    public function index(Request $request): Response
    {
        return static::buildSitemap()->toResponse($request);
    }
}
