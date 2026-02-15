<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CV — {{ $developer->name }}</title>
    <style>
        /* ── ATS-Friendly Professional CV ── */
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Georgia', 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #1a1a1a;
            max-width: 210mm;
            margin: 0 auto;
            padding: 18mm 20mm;
            background: #fff;
        }

        /* ── Header ── */
        .cv-header {
            text-align: center;
            padding-bottom: 14pt;
            margin-bottom: 16pt;
            border-bottom: 2pt solid #1a1a1a;
        }

        .cv-name {
            font-family: 'Helvetica Neue', Arial, Helvetica, sans-serif;
            font-size: 24pt;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            margin-bottom: 4pt;
            color: #1a1a1a;
        }

        .cv-title {
            font-family: 'Helvetica Neue', Arial, Helvetica, sans-serif;
            font-size: 12pt;
            font-weight: 400;
            color: #444;
            margin-bottom: 10pt;
            letter-spacing: 0.02em;
        }

        .cv-contact-line {
            font-family: 'Helvetica Neue', Arial, Helvetica, sans-serif;
            font-size: 9.5pt;
            color: #333;
            line-height: 1.8;
        }

        .cv-contact-line a {
            color: #1a1a1a;
            text-decoration: none;
        }

        .cv-contact-line a:hover {
            text-decoration: underline;
        }

        .cv-separator {
            color: #999;
            margin: 0 6pt;
        }

        /* ── Section ── */
        .cv-section {
            margin-bottom: 14pt;
        }

        .cv-section-title {
            font-family: 'Helvetica Neue', Arial, Helvetica, sans-serif;
            font-size: 11pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #1a1a1a;
            border-bottom: 1pt solid #1a1a1a;
            padding-bottom: 3pt;
            margin-bottom: 8pt;
        }

        .cv-section-content {
            font-size: 10.5pt;
            color: #2a2a2a;
            text-align: justify;
        }

        .cv-section-content p {
            margin-bottom: 6pt;
        }

        .cv-section-content p:last-child {
            margin-bottom: 0;
        }

        /* ── Skills (plain text, comma-separated for ATS) ── */
        .cv-skills-list {
            font-family: 'Helvetica Neue', Arial, Helvetica, sans-serif;
            font-size: 10.5pt;
            color: #2a2a2a;
            line-height: 1.7;
        }

        /* ── Projects ── */
        .cv-project {
            margin-bottom: 10pt;
        }

        .cv-project:last-child {
            margin-bottom: 0;
        }

        .cv-project-title {
            font-family: 'Helvetica Neue', Arial, Helvetica, sans-serif;
            font-size: 10.5pt;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 2pt;
        }

        .cv-project-desc {
            font-size: 10.5pt;
            color: #2a2a2a;
            margin: 0;
            padding-left: 12pt;
        }

        /* ── Meta Info ── */
        .cv-meta-row {
            font-family: 'Helvetica Neue', Arial, Helvetica, sans-serif;
            font-size: 9.5pt;
            color: #444;
            text-align: center;
            margin-bottom: 6pt;
        }

        /* ── Salary ── */
        .cv-salary {
            font-family: 'Helvetica Neue', Arial, Helvetica, sans-serif;
            font-size: 10.5pt;
            color: #2a2a2a;
        }

        /* ── Actions (screen only) ── */
        .cv-actions {
            margin-top: 20pt;
            padding-top: 12pt;
            border-top: 1pt solid #ccc;
            text-align: center;
        }

        .cv-actions .btn {
            display: inline-block;
            padding: 8pt 18pt;
            margin: 0 4pt;
            background: #1a1a1a;
            color: #fff;
            text-decoration: none;
            border-radius: 4pt;
            font-family: 'Helvetica Neue', Arial, Helvetica, sans-serif;
            font-size: 9.5pt;
            font-weight: 600;
            letter-spacing: 0.02em;
        }

        .cv-actions .btn:hover {
            background: #333;
        }

        .cv-actions .btn-outline {
            background: transparent;
            color: #1a1a1a;
            border: 1pt solid #1a1a1a;
        }

        .cv-actions .btn-outline:hover {
            background: #f5f5f5;
        }

        /* ── Print ── */
        @media print {
            body {
                padding: 14mm 16mm;
            }
            .cv-actions {
                display: none !important;
            }
            a {
                color: #1a1a1a;
            }
        }
    </style>
</head>
<body>

    {{-- ── Header ── --}}
    <header class="cv-header">
        <h1 class="cv-name">{{ $developer->name }}</h1>
        <p class="cv-title">{{ $developer->jobTitle?->name ?? 'Software Developer' }}</p>

        <div class="cv-contact-line">
            <a href="mailto:{{ $developer->email }}">{{ $developer->email }}</a>
            @if($developer->phone)
                <span class="cv-separator">|</span>{{ $developer->phone }}
            @endif
            @if($developer->location)
                <span class="cv-separator">|</span>{{ $developer->location->getLabel() }}
            @endif
        </div>

        @if($developer->years_of_experience !== null || $developer->is_available || ($developer->availability_type && count($developer->availability_type) > 0))
            <div class="cv-meta-row">
                @php $metaItems = []; @endphp
                @if($developer->years_of_experience !== null)
                    @php $metaItems[] = $developer->years_of_experience . ' ' . Str::plural('year', $developer->years_of_experience) . ' of experience'; @endphp
                @endif
                @if($developer->is_available)
                    @php $metaItems[] = 'Available for hire'; @endphp
                @endif
                @if($developer->availability_type && count($developer->availability_type) > 0)
                    @php $metaItems[] = collect($developer->availability_type)->map(fn ($t) => $t->getLabel())->join(', '); @endphp
                @endif
                {{ implode('  |  ', $metaItems) }}
            </div>
        @endif
    </header>

    {{-- ── Professional Summary ── --}}
    @if($developer->bio)
        <section class="cv-section">
            <h2 class="cv-section-title">Professional Summary</h2>
            <div class="cv-section-content">
                <p>{{ $developer->bio }}</p>
            </div>
        </section>
    @endif

    {{-- ── Technical Skills ── --}}
    @if($developer->skills->isNotEmpty())
        <section class="cv-section">
            <h2 class="cv-section-title">Technical Skills</h2>
            <div class="cv-skills-list">
                {{ $developer->skills->pluck('name')->join('  ·  ') }}
            </div>
        </section>
    @endif

    {{-- ── Projects ── --}}
    @if($developer->projects->isNotEmpty())
        <section class="cv-section">
            <h2 class="cv-section-title">Projects</h2>
            @foreach($developer->projects as $project)
                <div class="cv-project">
                    <h3 class="cv-project-title">{{ $project->title }}</h3>
                    @if($project->description)
                        <p class="cv-project-desc">{{ $project->description }}</p>
                    @endif
                </div>
            @endforeach
        </section>
    @endif
</body>
</html>
