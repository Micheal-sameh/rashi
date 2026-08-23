# Rashi — Design Brief for New UI (Stitch)

## 1. What the App Is

Rashi is an **admin back-office web application** for managing a church/youth-group points, competitions, and rewards program. It is **bilingual (Arabic/English) with full RTL support**. Admins manage members (organized into "families" and "groups"), run trivia/quiz competitions, award bonus/penalty points, let members redeem rewards through a points-based order system, and track every change via audit logs.

This app is admin-facing only — the member/servant experience lives in a separate mobile app that talks to the API, so this brief covers the **admin dashboard UI**, not the mobile app.

## 2. User Roles

- **Admin** — the only role with access to the dashboard. Full access to all screens below.
- **Guest** — unauthenticated; only sees login / password reset screens.

## 3. Global Shell / Navigation

- Persistent **left sidebar** (desktop), collapsing to a slide-in overlay + top mobile header on smaller screens.
- Sidebar sections: User Management, Competitions, Points & Rewards, System Settings, Content Management.
- Sidebar shows live badge counts (e.g. pending bonus/penalty approvals, pending orders).
- Global success/error/validation alert banners at top of content area.
- Shared delete-confirmation modal, and an image lightbox/zoom pattern used across pages.
- Must mirror correctly in RTL (Arabic) mode — nav, icons, paddings, text alignment.

## 4. Recurring Page Patterns (design these as reusable templates)

1. **List / table page**
   - Header: title + short description + primary "Create" button (top right, or top-start in RTL).
   - Filter bar: search input, dropdown filters, submit button.
   - Desktop: data table with header row, hover states, pagination, per-row actions (view/edit/delete).
   - Mobile (<992px): table collapses into stacked cards.
   - Empty state: illustration + message + CTA when no records.
2. **Create / Edit form page**
   - Centered card, one or two-column responsive field grid.
   - Large touch-friendly inputs, icon-prefixed labels.
   - Image upload with preview where relevant.
   - Inline validation messages, submit/cancel buttons.
3. **Detail / show page**
   - Profile-style header (avatar/name/badges) + stat cards + related-records list.
4. **Modal**
   - Delete confirmation, clone-with-date-picker, image lightbox.
5. **Dashboard stat cards**
   - Small KPI cards (e.g. "Total Admins", "Pending Approvals") sitting above a list.
6. **Flowchart/diagram view**
   - Non-tabular visual page showing quiz/competition sequencing, exportable to PDF.

## 5. Full Screen Inventory

### Auth (Guest)
- **Login** — centered card, email + password, submit.
- **Forgot Password** — centered card, email input, "send reset link" CTA.
- **Reset Password** — centered card, new password + confirm, token-based.

### User Management
- **Users list** — searchable/filterable/sortable table of all members, group filter, export to Excel, links to Admin Users and Leaderboard.
- **Admin Users** — filtered list of admin-role users, stat card ("Total Admins"), search by name/email/code.
- **User Detail** — single member profile: points, groups, history.
- **Leaderboard** — ranked list by score/points; export to PDF and Excel.
- **Families list** — search families by membership code.
- **Family Detail** — all members of a family with score/points/quiz stats, last reward/bonus/penalty/competition, export to Excel.
- **User History** — search a user, view full point transaction history (debit/credit totals).

### Competitions & Quizzes
- **Competitions list** — table with status, edit/export/clone/activate/cancel actions, Excel import/export.
- **Create/Edit Competition** — form with Excel quiz upload, group assignment, date range.
- **Quizzes Flowchart** — visual diagram of quiz sequencing per competition (+ PDF export).
- **User Answers** — per-competition list of member quiz answers/results.
- **Leaderboard PDF export** — printable ranking document.
- **Quizzes list** — CRUD list of quizzes.
- **Create/Edit Quiz** — quiz form.
- **Quiz Questions list** — questions per quiz.
- **Create/Edit Question** — question form with answer options, image upload/preview, correct-answer marker.

### Points, Rewards & Orders
- **Bonus/Penalty list** — approved records, search/filter.
- **Pending Approvals** — queue of pending bonus/penalty entries, approve/reject actions, live badge count.
- **Create Bonus/Penalty** — form to assign a points adjustment to a member.
- **Bonus/Penalty Detail** — record view (creator, approver, amount, reason).
- **Point Transfers list** — record of point transfers between family members.
- **Create Point Transfer** — form with family-member lookup, max-transferable validation.
- **Point Transfer Detail**.
- **Rewards list** — catalog of redeemable rewards; add-quantity/activate/cancel actions.
- **Create Reward** — form with name, points cost, quantity, image.
- **Orders list** — redemption orders queue; mark received/cancel; real-time updates; pending-count badge.

### System / Content Management
- **Settings** — app-wide settings (logo, token management incl. "delete all tokens").
- **Groups list** — member groups/teams.
- **Create/Edit Group**.
- **Group Users Edit** — assign/remove members within a group.
- **Groups × Competitions matrix** — cross-view of group participation, PDF flowchart export.
- **Notifications list** — sent notifications log.
- **Create Notification** — compose/send push notification.
- **About Us** — show/edit static content.
- **Terms** — show/edit static content.
- **Social Media** — list/edit social links & icons.
- **Info Videos** — list/create/edit, drag-and-drop rank reordering.
- **Audit Logs list** — filterable log (action type, model, date range), paginated.
- **Audit Log Detail** — old-value vs new-value diff view.

## 6. Existing Branding to Carry Forward (or deliberately refresh)

- **Font**: Inter (300–700), system-font fallback.
- **Component base**: Bootstrap 5.3 conventions (cards, tables, alerts, badges, modals) + Font Awesome icons.
- **Color gradients**:
  - Primary: indigo → purple (`#667eea` → `#764ba2`)
  - Success: teal → green (`#11998e` → `#38ef7d`)
  - Danger: red → orange (`#fc4a1a` → `#f7b733`)
  - Warning: pink → red (`#f093fb` → `#f5576c`)
  - Info: blue → cyan (`#4facfe` → `#00f2fe`)
  - Sidebar: deep indigo/navy (`#1a237e` → `#283593`)
  - Page background: light gray gradient (`#f8fafc` → `#e2e8f0`)
- **Shape language**: large border radius (16px cards/tables, 10px buttons/inputs, 20px pill badges).
- **Shadows**: soft ambient shadow at rest, deeper shadow + slight lift on hover.
- **No hard borders**: cards/buttons rely on shadow, not outlines; inputs use light gray border + purple focus glow.
- **RTL/i18n**: full Arabic mirroring is a hard requirement, not optional polish.
- **Layout**: fixed 260px sidebar desktop, full-width content (not centered/max-width), tables → stacked cards below 992px.

## 7. What to Ask Stitch For

Use this brief to generate the following as a first pass:
1. A **design system** (colors, type scale, spacing, radii, shadows, button/input/badge/table styles) — either refresh the current gradient-heavy indigo/purple theme or propose a cleaner alternative, but keep RTL support and the table→card mobile pattern as constraints.
2. The **sidebar shell** (desktop + mobile) with the nav sections listed in §3.
3. **Template screens** for each pattern in §4 (list page, form page, detail page, stat-card dashboard, flowchart page) — these templates cover ~90% of the screens in §5, so get these right first.
4. A handful of **representative real screens** to validate the templates: Users list, Create Competition, Pending Approvals, Family Detail, Audit Logs list.
