{{--
    Shared field set for creating and editing a FAQ entry.

    Expects: $faq (App\Models\Faq|null), $categories (Illuminate\Support\Collection), $services (Illuminate\Support\Collection)
--}}
<div class="tone">
    <div class="row g-3">
        <div class="col-md-8">
            <label class="form-label" for="faqQuestion">Question</label>
            <input class="form-control @error('question') input-error @enderror"
                   id="faqQuestion"
                   name="question"
                   value="{{ old('question', $faq?->question) }}"
                   maxlength="255"
                   required
                   placeholder="e.g. Do you take walk-ins for threading?">
            @error('question') <div class="field-error">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-4">
            <label class="form-label" for="faqOrder">Display order</label>
            <input class="form-control @error('sort_order') input-error @enderror"
                   id="faqOrder"
                   name="sort_order"
                   type="number"
                   value="{{ old('sort_order', $faq?->sort_order ?? 0) }}">
            @error('sort_order') <div class="field-error">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label" for="faqCategory">Category group</label>
            <select class="form-select @error('category_id') input-error @enderror" id="faqCategory" name="category_id">
                <option value="">— No group —</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}"
                            @selected((string) old('category_id', $faq?->category_id) === (string) $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id') <div class="field-error">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label" for="faqNewCategory">Or create a group</label>
            <input class="form-control @error('new_category') input-error @enderror"
                   id="faqNewCategory"
                   name="new_category"
                   value="{{ old('new_category') }}"
                   placeholder="e.g. Hygiene">
            @error('new_category') <div class="field-error">{{ $message }}</div> @enderror
        </div>

        <div class="col-12">
            <label class="form-label" for="faqAnswer">Answer</label>
            <textarea class="form-control @error('answer') input-error @enderror"
                      id="faqAnswer"
                      name="answer"
                      rows="6"
                      required
                      placeholder="Clear, concise answer under 60 words…">{{ old('answer', $faq?->answer) }}</textarea>
            @error('answer') <div class="field-error">{{ $message }}</div> @enderror
            <p class="tiny muted mt-1 mb-0">Basic HTML is allowed and sanitised on save. Answers also feed the FAQ structured data.</p>
        </div>

        <div class="col-12">
            <label class="form-label" for="faqServices">Show on service pages</label>
            <select class="form-select @error('service_ids') input-error @enderror" id="faqServices" name="service_ids[]" multiple size="6">
                @foreach($services as $service)
                    <option value="{{ $service->id }}"
                            @selected(in_array($service->id, old('service_ids', $faq?->services->pluck('id')->toArray() ?? [])))>
                        {{ $service->name }}
                    </option>
                @endforeach
            </select>
            @error('service_ids') <div class="field-error">{{ $message }}</div> @enderror
            <p class="tiny muted mt-1 mb-0">Hold Ctrl (or Cmd) to select several. Leave empty to show the answer on the FAQ page only.</p>
        </div>
    </div>
</div>

<div>
    <div class="tone section-gap">
        <h3>Visibility</h3>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="faqActive" name="is_published" value="1"
                   @checked(old('is_published', $faq?->is_published ?? false))>
            <label class="form-check-label small" for="faqActive">Published on website</label>
        </div>
        <p class="tiny muted mt-2 mb-0">Unchecking keeps the answer saved in admin without outputting it to the public site or the Schema.org block.</p>
    </div>

    <div class="tone section-gap">
        <h3>Search &amp; social</h3>

        <label class="form-label" for="faqMetaTitle">Meta title</label>
        <input class="form-control form-control-sm @error('meta_title') input-error @enderror"
               id="faqMetaTitle"
               name="meta_title"
               value="{{ old('meta_title', $faq?->meta_title) }}"
               maxlength="255">
        @error('meta_title') <div class="field-error">{{ $message }}</div> @enderror

        <label class="form-label mt-3" for="faqMetaDescription">Meta description</label>
        <textarea class="form-control form-control-sm @error('meta_description') input-error @enderror"
                  id="faqMetaDescription"
                  name="meta_description"
                  rows="3"
                  maxlength="500">{{ old('meta_description', $faq?->meta_description) }}</textarea>
        @error('meta_description') <div class="field-error">{{ $message }}</div> @enderror
    </div>
</div>
