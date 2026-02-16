# Project Work Summary

A snapshot of development activity on **Find Developer** — commits, branches, and scope of work.

---

## At a glance

| Metric | Value |
|--------|--------|
| **Total commits** | 341 |
| **Active branches** | 60+ (local & remote) |
| **Merged PRs** | 74+ |
| **Project start** | 2026-01-15 |
| **Last activity** | 2026-02-16 |
| **Active period** | ~1 month |

---

## Branches by type

Branches show how work was split into features, fixes, and improvements.

### Features (`feature/*`)

| Branch | Purpose |
|--------|---------|
| `feature/job-offers` | Job offers functionality |
| `feature/ai` | AI integration |
| `feature/ai-prompt` | AI prompt for easier company search |
| `feature/cv-generator` | CV generator |
| `feature/cv-upload` | CV PDF upload |
| `feature/developer-companies` | Developer work experience |
| `feature/devleoper-offer` | Developer offer for HR users |
| `feature/favicon` | Favicons |
| `feature/recommendation-processing` | Process recommendations (emails) |
| `feature/resource-badges` | Pending badges in Filament resources |
| `feature/special-need-developers` | Special needs developers section |
| `feature/api` | Developer API |
| `feature/mail` / `feature/mailtrap` | Email (Mailtrap) |
| `feature/typesense` | Typesense |
| `feature/using-vue` | Vue usage |
| `feature/new-color-platte` | New color palette |
| `feature/new-logo` | New logo |
| `feature/redesign` | Redesign |
| `feature/prism` | Prism |

### Enhancements (`enhancement/*`)

| Branch | Purpose |
|--------|---------|
| `enhancement/about-us` | About us page & contact |
| `enhancement/back-to-top` | Back to top (free section) |
| `enhancement/company-parent` | Parent company & promotions |
| `enhancement/cv-link` | Tooltip & CV link on developer card |
| `enhancement/editable-years` | Edit years of experience |
| `enhancement/filter-resource` | “Doesn’t have badge” filter |
| `enhancement/filters` | Filter buttons |
| `enhancement/icon-picker` | Icon picker in Blade |
| `enhancement/login` | Redirect to home on login |
| `enhancement/login-page` | Login page subheading |
| `enhancement/main-button` | Guest → register, auth → dashboard |
| `enhancement/nav-active-state` | Nav active state & styles |
| `enhancement/nightwatch` | Nightwatch |
| `enhancement/open-source` | Open source star banner |
| `enhancement/policy` | Policies |
| `enhancement/recommend-indicator` | Recommend indicator on developer card |
| `enhancement/recommended-by-us` | Recommended by us page |
| `enhancement/recommender-email` | Emails to recommenders |
| `enhancement/repo-public` | Link to platform GitHub repo |
| `enhancement/reset-password` | Password reset |
| `enhancement/style` | Style (e.g. GitHub icon in dark mode) |

### Fixes (`fix/*`)

| Branch | Purpose |
|--------|---------|
| `fix/count` | Developer badge count |
| `fix/dompdf` | Use DomPDF instead of browser shot |
| `fix/filter-query` | Filter query (where/function) |
| `fix/iframe` | Remove CV iframe |
| `fix/is-recommended` | Only recommended filter |
| `fix/label` | Government labels |
| `fix/work-experience-count` | Work experience months count |

### Refactors (`refactor/*`)

| Branch | Purpose |
|--------|---------|
| `refactor/developer-registration` | Developer registration & search |
| `refactor/domain-name` | HTTPS / www handling |
| `refactor/phone` | Display phone when exists |
| `refactor/pint` | Run Pint & add to Composer |
| `refactor/work-validated-badge` | Experience validated badge as env variable |

### Other branches

- **Main workflow:** `main`, `work`, `dev`, `uat`, `test`
- **Design/UI:** `developer-projects`, `main-new-design`, `old-design`, `old-main`, `enhancement/new-design`, `enhancement/hide-more`, `enhancement/salary`
- **PR branches:** `pr-11-local`, `pr-16`, `pr-50`, `pr-51`, `pr-52`, `pr-53`, `pr-73`

---

## Commit history (high level)

Non-merge commits that represent actual feature/fix work. Merge commits are omitted.

### 2026-02-16

- Add it to missing files and remove some duplication  
- Update style  
- Fix styling  
- Adding developer offer ability for HR users  
- sec: add rel="noopener noreferrer" to external links  
- Using floor over round  
- Update how to count the months in work experience  
- Adding tooltip and CV link to developer card  
- Install Nightwatch  
- Remove Laravel Telescope  

### 2026-02-15

- Make filter inside a where case with a function  
- Adding parent functionality for promotion in single company  
- If parent selected, use parent company name  
- Adding action to process recommendation by sending emails  
- If guest show register, if auth go to dashboard  
- Adding policies  
- Adding feature to developer (work experience)  
- Update government labels  
- Using DomPDF instead of browser shot  
- Adding CV generator  
- Remove iframe of CV  
- Update Livewire  
- Adding ability to add CV PDF files to the system  

### 2026-02-14

- Update where the AI prompt exists  
- Adding AI prompt feature to make search easier for companies  
- Fix bug in recent filter  
- Adding “doesn’t have badge” filter in developer resource  
- Conditionally render “Read more” based on task requirements  
- Refactor contact email handling  
- Exclude global scopes for project queries (DeveloperProfileController)  
- Adjust email verification banner visibility  
- Developer status tabs & navbar auth flow  
- Adding other contact on about us page  
- Adding icon picker and use in Blade view  
- Only the recommended filter  
- Adding recommend indicator to developer card  
- Adding nav active state with styles  
- Update recommended by us page  

### 2026-02-12

- Adding get search result  
- Refactor developer registration  

### 2026-02-11

- Update subheading of login page  
- Fix developer badge count  
- Adding section for special needs developers  
- Update filters button  
- When success login redirect to home page  
- Sending emails to recommenders  
- Redesign (Hussein Alaa)  

### 2026-02-10

- Adding pending badges to Filament resources  
- Adding ability to reset password  
- Making experience validated badge an env variable  
- Adding ability to edit years of experience  
- Refine navbar banners (info banner, email banner, mobile marquee)  
- Adding License  
- Move info banner inside fixed navbar  
- Adding new favicons  
- Adding https instead of just www  
- Display the phone number if exists  
- Fix GitHub icon style on dark mode  
- Adding back to top for free section  
- Run Pint and add to Composer  
- Change banner to open source star motivation  
- Adding link to platform GitHub repo  

### 2026-02-08 – 2026-02-09

- Adding developer profile page  
- Update color palette  
- Add new logo  
- Adding click action to chart and fix remember me  
- API for developer  

### 2026-01-15 – 2026-02-07 (selected)

- Adding custom domain ending with .iq  
- Add badges route  
- Adding Developer Observer to the model  
- Adding Mailtrap email functionality  
- Adding requirement column  
- Adding experience tasks feature  
- Average salary in Iraq  
- Adding telescope  
- Adding charts for Iraqi developers  
- Adding developer-to-developer recommendation  
- Add show more functionality  
- Adding badges functionality  
- Adding custom error pages  
- Adding create user action  
- Adding user services and appointments  
- Adding recommended developers feature  
- Adding job offers functionality  
- Adding GitHub workflows  
- Use developer slug instead of id  
- Adding developer profile page  
- Adding dark mode and light mode  
- Adding developer projects  
- Adding functionality for developer projects  
- Server-side search  
- Adding SEO helpers  
- Adding students plans  
- Pagination UI  
- Developers can access admin dashboard  
- Adding more currencies and governorates  
- Adding about us page  
- Adding pricing page  
- Adding developer project workflow  
- Sticky filters, filters as floating button  
- Premium and pro developers  
- Separate seeder and skill resource  
- **Adding the first MVP launch**  
- **First commit**  

---

## Full commit log (oneline)

For a full list of every commit (including merges):

```bash
git log --oneline --all
```

Or with date and author:

```bash
git log --all --format="%h %ad %an: %s" --date=short
```

---

*Generated from the project’s git history. Use the commands above to regenerate or inspect up-to-date history.*
