{{--
    Shared field set for creating and editing an article.

    Expects: $post (App\Models\Post|null), $categories (Illuminate\Support\Collection)
--}}
<div class="tone">
    <div class="row g-3">
        <div class="col-md-8">
            <label class="form-label" for="postTitle">Title</label>
            <input class="form-control @error('title') input-error @enderror"
                   id="postTitle"
                   name="title"
                   value="{{ old('title', $post?->title) }}"
                   required>
            @error('title') <div class="field-error">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-4">
            <label class="form-label" for="postSlug">Slug</label>
            <input class="form-control @error('slug') input-error @enderror"
                   id="postSlug"
                   name="slug"
                   value="{{ old('slug', $post?->slug) }}"
                   placeholder="auto-generated from title">
            @error('slug') <div class="field-error">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label" for="postCategory">Category</label>
            <select class="form-select @error('category_id') input-error @enderror" id="postCategory" name="category_id">
                <option value="">— No category —</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}"
                            @selected((string) old('category_id', $post?->category_id) === (string) $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id') <div class="field-error">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label" for="postNewCategory">Or create a category</label>
            <input class="form-control @error('new_category') input-error @enderror"
                   id="postNewCategory"
                   name="new_category"
                   value="{{ old('new_category') }}"
                   placeholder="e.g. Aftercare">
            @error('new_category') <div class="field-error">{{ $message }}</div> @enderror
        </div>

        <div class="col-12">
            <label class="form-label" for="postExcerpt">Excerpt</label>
            <textarea class="form-control @error('excerpt') input-error @enderror"
                      id="postExcerpt"
                      name="excerpt"
                      rows="2"
                      maxlength="1000"
                      placeholder="Short summary used on cards and in search results.">{{ old('excerpt', $post?->excerpt) }}</textarea>
            @error('excerpt') <div class="field-error">{{ $message }}</div> @enderror
        </div>

        <div class="col-12">
            <label class="form-label" for="postContent">Content</label>
            <textarea class="form-control @error('content') input-error @enderror"
                      id="postContent"
                      name="content"
                      rows="12"
                      required
                      placeholder="Write the article. Basic HTML is allowed and sanitised on save.">{{ old('content', $post?->content) }}</textarea>
            @error('content') <div class="field-error">{{ $message }}</div> @enderror
            <p class="tiny muted mt-1 mb-0">Scripts and unsafe markup are stripped automatically when the article is saved.</p>
        </div>

        <div class="col-md-6">
            <label class="form-label" for="postFeaturedImage">Featured image</label>
            <input class="form-control @error('featured_image') input-error @enderror"
                   id="postFeaturedImage"
                   name="featured_image"
                   type="file"
                   accept="image/jpeg,image/png,image/webp,image/gif">
            @error('featured_image') <div class="field-error">{{ $message }}</div> @enderror
            <p class="tiny muted mt-1 mb-0">JPEG, PNG, WebP or GIF · max 5 MB.</p>
            @if($post?->featuredImageUrl())
                <img src="{{ $post->featuredImageUrl() }}" alt="Current featured image" class="mt-2" style="max-width:100%;border-radius:12px">
            @endif
        </div>

        <div class="col-md-6">
            <label class="form-label" for="postFeaturedImageAlt">Featured image alt text</label>
            <input class="form-control @error('featured_image_alt') input-error @enderror"
                   id="postFeaturedImageAlt"
                   name="featured_image_alt"
                   value="{{ old('featured_image_alt', $post?->featured_image_alt) }}"
                   placeholder="Describe the image for screen readers">
            @error('featured_image_alt') <div class="field-error">{{ $message }}</div> @enderror
        </div>
    </div>
</div>

<div>
    <div class="tone section-gap">
        <h3>Publishing</h3>

        <label class="form-label" for="postStatus">Status</label>
        <select class="form-select form-select-sm mb-3 @error('status') input-error @enderror" id="postStatus" name="status">
            @foreach(\App\Enums\PostStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected(old('status', $post?->status?->value ?? 'draft') === $status->value)>
                    {{ $status->label() }}
                </option>
            @endforeach
        </select>
        @error('status') <div class="field-error">{{ $message }}</div> @enderror

        <label class="form-label" for="postPublishedAt">Publish date</label>
        <input class="form-control form-control-sm @error('published_at') input-error @enderror"
               id="postPublishedAt"
               name="published_at"
               type="datetime-local"
               value="{{ old('published_at', $post?->published_at?->format('Y-m-d\TH:i')) }}">
        @error('published_at') <div class="field-error">{{ $message }}</div> @enderror
        <p class="tiny muted mt-2 mb-0">Leave empty for draft and scheduled articles. Publishing without a date publishes immediately.</p>
    </div>

    <div class="tone section-gap">
        <h3>Search &amp; social</h3>

        <label class="form-label" for="postMetaTitle">Meta title</label>
        <input class="form-control form-control-sm @error('meta_title') input-error @enderror"
               id="postMetaTitle"
               name="meta_title"
               value="{{ old('meta_title', $post?->meta_title) }}"
               maxlength="255">
        @error('meta_title') <div class="field-error">{{ $message }}</div> @enderror

        <label class="form-label mt-3" for="postMetaDescription">Meta description</label>
        <textarea class="form-control form-control-sm @error('meta_description') input-error @enderror"
                  id="postMetaDescription"
                  name="meta_description"
                  rows="3"
                  maxlength="500">{{ old('meta_description', $post?->meta_description) }}</textarea>
        @error('meta_description') <div class="field-error">{{ $message }}</div> @enderror

        <label class="form-label mt-3" for="postCanonicalUrl">Canonical URL</label>
        <input class="form-control form-control-sm @error('canonical_url') input-error @enderror"
               id="postCanonicalUrl"
               name="canonical_url"
               value="{{ old('canonical_url', $post?->canonical_url) }}"
               placeholder="https://…">
        @error('canonical_url') <div class="field-error">{{ $message }}</div> @enderror

        <label class="form-label mt-3" for="postOgTitle">OG title</label>
        <input class="form-control form-control-sm @error('og_title') input-error @enderror"
               id="postOgTitle"
               name="og_title"
               value="{{ old('og_title', $post?->og_title) }}"
               maxlength="255">
        @error('og_title') <div class="field-error">{{ $message }}</div> @enderror

        <label class="form-label mt-3" for="postOgDescription">OG description</label>
        <textarea class="form-control form-control-sm @error('og_description') input-error @enderror"
                  id="postOgDescription"
                  name="og_description"
                  rows="2"
                  maxlength="500">{{ old('og_description', $post?->og_description) }}</textarea>
        @error('og_description') <div class="field-error">{{ $message }}</div> @enderror

        <label class="form-label mt-3" for="postOgImage">OG image</label>
        <input class="form-control form-control-sm @error('og_image') input-error @enderror"
               id="postOgImage"
               name="og_image"
               type="file"
               accept="image/jpeg,image/png,image/webp,image/gif">
        @error('og_image') <div class="field-error">{{ $message }}</div> @enderror

        <label class="form-label mt-3" for="postTwitterCard">Twitter card</label>
        <select class="form-select form-select-sm @error('twitter_card') input-error @enderror" id="postTwitterCard" name="twitter_card">
            @foreach(['summary', 'summary_large_image'] as $card)
                <option value="{{ $card }}" @selected(old('twitter_card', $post?->twitter_card ?? 'summary_large_image') === $card)>
                    {{ $card === 'summary' ? 'Small summary card' : 'Large image card' }}
                </option>
            @endforeach
        </select>
        @error('twitter_card') <div class="field-error">{{ $message }}</div> @enderror
    </div>
</div>
