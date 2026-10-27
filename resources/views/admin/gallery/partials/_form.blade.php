{{--
    Shared field set for creating and editing a gallery item.

    Expects: $galleryItem (App\Models\GalleryItem|null), $categories (Illuminate\Support\Collection)
--}}
<div class="tone">
    <div class="row g-3">
        <div class="col-md-8">
            <label class="form-label" for="galleryTitle">Title</label>
            <input class="form-control @error('title') input-error @enderror"
                   id="galleryTitle"
                   name="title"
                   value="{{ old('title', $galleryItem?->title) }}"
                   required>
            @error('title') <div class="field-error">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-4">
            <label class="form-label" for="gallerySlug">Slug</label>
            <input class="form-control @error('slug') input-error @enderror"
                   id="gallerySlug"
                   name="slug"
                   value="{{ old('slug', $galleryItem?->slug) }}"
                   placeholder="auto-generated from title">
            @error('slug') <div class="field-error">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label" for="galleryCategory">Category</label>
            <select class="form-select @error('category_id') input-error @enderror" id="galleryCategory" name="category_id">
                <option value="">— No category —</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}"
                            @selected((string) old('category_id', $galleryItem?->category_id) === (string) $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id') <div class="field-error">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label" for="galleryNewCategory">Or create a category</label>
            <input class="form-control @error('new_category') input-error @enderror"
                   id="galleryNewCategory"
                   name="new_category"
                   value="{{ old('new_category') }}"
                   placeholder="e.g. Brow &amp; Threading">
            @error('new_category') <div class="field-error">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label" for="galleryMediaType">Media type</label>
            <select class="form-select @error('media_type') input-error @enderror" id="galleryMediaType" name="media_type">
                @foreach(\App\Enums\GalleryMediaType::cases() as $type)
                    <option value="{{ $type->value }}" @selected(old('media_type', $galleryItem?->media_type?->value ?? 'image') === $type->value)>
                        {{ $type->label() }}
                    </option>
                @endforeach
            </select>
            @error('media_type') <div class="field-error">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label" for="gallerySortOrder">Display order</label>
            <input class="form-control @error('sort_order') input-error @enderror"
                   id="gallerySortOrder"
                   name="sort_order"
                   type="number"
                   value="{{ old('sort_order', $galleryItem?->sort_order ?? 0) }}">
            @error('sort_order') <div class="field-error">{{ $message }}</div> @enderror
        </div>

        <div class="col-12">
            <label class="form-label" for="galleryAltText">Alt text</label>
            <input class="form-control @error('alt_text') input-error @enderror"
                   id="galleryAltText"
                   name="alt_text"
                   value="{{ old('alt_text', $galleryItem?->alt_text) }}"
                   maxlength="255"
                   required
                   placeholder="Describe the image for screen readers">
            @error('alt_text') <div class="field-error">{{ $message }}</div> @enderror
        </div>

        <div class="col-12">
            <label class="form-label" for="galleryCaption">Caption</label>
            <input class="form-control @error('caption') input-error @enderror"
                   id="galleryCaption"
                   name="caption"
                   value="{{ old('caption', $galleryItem?->caption) }}"
                   maxlength="255">
            @error('caption') <div class="field-error">{{ $message }}</div> @enderror
        </div>

        <div class="col-12">
            <label class="form-label" for="galleryDescription">Description</label>
            <textarea class="form-control @error('description') input-error @enderror"
                      id="galleryDescription"
                      name="description"
                      rows="3">{{ old('description', $galleryItem?->description) }}</textarea>
            @error('description') <div class="field-error">{{ $message }}</div> @enderror
        </div>
    </div>
</div>

<div>
    <div class="tone section-gap">
        <h3>Media</h3>

        <label class="form-label" for="galleryImage">Image</label>
        <input class="form-control form-control-sm @error('image') input-error @enderror"
               id="galleryImage"
               name="image"
               type="file"
               accept="image/jpeg,image/png,image/webp,image/gif">
        @error('image') <div class="field-error">{{ $message }}</div> @enderror
        <p class="tiny muted mt-1 mb-0">JPEG, PNG, WebP or GIF · max 5 MB. A thumbnail is generated automatically.</p>
        @if($galleryItem?->imageUrl())
            <img src="{{ $galleryItem->imageUrl() }}" alt="Current gallery image" class="mt-2" style="max-width:100%;border-radius:12px">
        @endif

        <label class="form-label mt-3" for="galleryVideo">Video</label>
        <input class="form-control form-control-sm @error('video') input-error @enderror"
               id="galleryVideo"
               name="video"
               type="file"
               accept="video/mp4,video/quicktime">
        @error('video') <div class="field-error">{{ $message }}</div> @enderror
        <p class="tiny muted mt-1 mb-0">MP4 or MOV · max 50 MB · 120 seconds maximum. Duration is detected on upload.</p>
        @if($galleryItem?->videoUrl())
            <video src="{{ $galleryItem->videoUrl() }}" controls class="mt-2" style="max-width:100%;border-radius:12px"></video>
        @endif

        <label class="form-label mt-3" for="galleryThumbnail">Video poster / thumbnail</label>
        <input class="form-control form-control-sm @error('thumbnail') input-error @enderror"
               id="galleryThumbnail"
               name="thumbnail"
               type="file"
               accept="image/jpeg,image/png,image/webp,image/gif">
        @error('thumbnail') <div class="field-error">{{ $message }}</div> @enderror
        <p class="tiny muted mt-1 mb-0">Required for videos — we cannot extract a frame from the file.</p>
    </div>

    <div class="tone section-gap">
        <h3>Visibility</h3>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="galleryPublished" name="is_published" value="1"
                   @checked(old('is_published', $galleryItem?->is_published ?? false))>
            <label class="form-check-label small" for="galleryPublished">Published on website</label>
        </div>
        <p class="tiny muted mt-2 mb-0">Unpublished items stay in the admin panel but never reach the public gallery.</p>
    </div>

    <div class="tone section-gap">
        <h3>Search &amp; social</h3>

        <label class="form-label" for="galleryMetaTitle">Meta title</label>
        <input class="form-control form-control-sm @error('meta_title') input-error @enderror"
               id="galleryMetaTitle"
               name="meta_title"
               value="{{ old('meta_title', $galleryItem?->meta_title) }}"
               maxlength="255">
        @error('meta_title') <div class="field-error">{{ $message }}</div> @enderror

        <label class="form-label mt-3" for="galleryMetaDescription">Meta description</label>
        <textarea class="form-control form-control-sm @error('meta_description') input-error @enderror"
                  id="galleryMetaDescription"
                  name="meta_description"
                  rows="3"
                  maxlength="500">{{ old('meta_description', $galleryItem?->meta_description) }}</textarea>
        @error('meta_description') <div class="field-error">{{ $message }}</div> @enderror

        <label class="form-label mt-3" for="galleryCanonicalUrl">Canonical URL</label>
        <input class="form-control form-control-sm @error('canonical_url') input-error @enderror"
               id="galleryCanonicalUrl"
               name="canonical_url"
               value="{{ old('canonical_url', $galleryItem?->canonical_url) }}"
               placeholder="https://…">
        @error('canonical_url') <div class="field-error">{{ $message }}</div> @enderror
    </div>
</div>
