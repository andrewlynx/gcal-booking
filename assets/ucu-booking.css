/* ── UCU Collegium Booking — Frontend Styles ─────────────────────────── */

.ucu-booking {
    max-width: 100%;
    margin: 0;
    padding: 0;
    background: transparent;
    border: none;
    box-shadow: none;
    color: #111827;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
    font-size: .95rem;
}

.ucu-booking__title {
    margin: 0 0 28px;
    font-size: 1.5rem;
    font-weight: 700;
    color: #111827;
    letter-spacing: -.02em;
}

/* ── Степер ───────────────────────────────────────────────────────────── */
.ucu-stepper {
    display: flex;
    align-items: flex-start;
    justify-content: center;
    margin-bottom: 36px;
    flex-wrap: nowrap;
    overflow-x: auto;
    padding-bottom: 4px;
    gap: 0;
}

.ucu-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    min-width: 72px;
    max-width: 92px;
    text-align: center;
    flex-shrink: 0;
}

.ucu-step-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    border: 2px solid #d1d5db;
    background: #fff;
    color: #9ca3af;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .8rem;
    font-weight: 600;
    transition: background .25s, border-color .25s, color .25s;
    flex-shrink: 0;
}

.ucu-step.active .ucu-step-circle {
    border-color: #4caf50;
    color: #4caf50;
}

.ucu-step.done .ucu-step-circle {
    background: #4caf50;
    border-color: #4caf50;
    color: #fff;
}

.ucu-step-label {
    font-size: .68rem;
    color: #9ca3af;
    margin-top: 6px;
    line-height: 1.3;
    transition: color .25s;
}

.ucu-step.active .ucu-step-label,
.ucu-step.done .ucu-step-label {
    color: #4caf50;
}

.ucu-step-line {
    flex: 1;
    height: 2px;
    background: #d1d5db;
    margin-top: 15px;
    min-width: 8px;
    max-width: 40px;
    flex-shrink: 1;
    transition: background .25s;
}

.ucu-step-line.done {
    background: #4caf50;
}

/* ── Панелі кроків ────────────────────────────────────────────────────── */
.ucu-step-panel {
    display: none;
}

.ucu-step-panel.active {
    display: block;
}

/* ── Блок секції ──────────────────────────────────────────────────────── */
.ucu-booking__block {
    padding: 0;
    border-top: none;
}

.ucu-booking__block-title { display: none; }

/* ── Сітка полів ──────────────────────────────────────────────────────── */
.ucu-booking__grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 20px;
}

/* ── Поля ─────────────────────────────────────────────────────────────── */
.ucu-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.ucu-field--textarea,
.ucu-field--radio,
.ucu-field--checkbox,
.ucu-field--attachment,
.ucu-field--date,
.ucu-field--phone {
    grid-column: 1 / -1;
}

.ucu-field__label {
    font-size: .875rem;
    font-weight: 500;
    color: #374151;
    line-height: 1.5;
}

.ucu-field input[type="text"],
.ucu-field input[type="email"],
.ucu-field input[type="tel"],
.ucu-field input[type="url"],
.ucu-field select,
.ucu-field textarea {
    width: 100%;
    box-sizing: border-box;
    padding: 10px 14px;
    font-size: .95rem;
    font-family: inherit;
    border: 1.5px solid #d1d5db;
    border-radius: 8px;
    outline: none;
    transition: border-color .2s, box-shadow .2s;
    color: #111827;
    background: #f9fafb;
    -webkit-appearance: none;
    appearance: none;
}

.ucu-field select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%236b7280' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    padding-right: 36px;
}

.ucu-field textarea {
    resize: vertical;
    min-height: 110px;
}

.ucu-field input:focus,
.ucu-field select:focus,
.ucu-field textarea:focus {
    border-color: #4caf50;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(76,175,80,.12);
}

.ucu-field input.ucu-invalid,
.ucu-field select.ucu-invalid,
.ucu-field textarea.ucu-invalid {
    border-color: #ef4444;
    box-shadow: 0 0 0 3px rgba(239,68,68,.1);
}

/* ── Date picker ──────────────────────────────────────────────────────── */
.ucu-date-row {
    display: grid;
    grid-template-columns: 1fr 1.6fr 1.2fr;
    gap: 10px;
}

.ucu-date-part {
    padding: 10px 14px;
    font-size: .95rem;
    font-family: inherit;
    border: 1.5px solid #d1d5db;
    border-radius: 8px;
    outline: none;
    color: #111827;
    background: #f9fafb;
    width: 100%;
    box-sizing: border-box;
    -webkit-appearance: none;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%236b7280' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 10px center;
    padding-right: 30px;
    transition: border-color .2s, box-shadow .2s;
    cursor: pointer;
}

.ucu-date-part:focus {
    border-color: #4caf50;
    background-color: #fff;
    box-shadow: 0 0 0 3px rgba(76,175,80,.12);
}

.ucu-date-part.ucu-invalid {
    border-color: #ef4444;
    box-shadow: 0 0 0 3px rgba(239,68,68,.1);
}

/* ── Radio / Checkbox ─────────────────────────────────────────────────── */
.ucu-field__choices {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.ucu-choice {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    cursor: pointer;
    font-size: .9rem;
    color: #374151;
    line-height: 1.5;
    padding: 0;
    border: none;
    background: none;
    border-radius: 0;
    min-height: unset;
    overflow-wrap: anywhere;
}

.ucu-choice input[type="radio"],
.ucu-choice input[type="checkbox"] {
    width: 16px;
    height: 16px;
    margin-top: 3px;
    flex-shrink: 0;
    accent-color: #4caf50;
    cursor: pointer;
}

/* ── Файл ─────────────────────────────────────────────────────────────── */
.ucu-file-wrap {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.ucu-file-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 11px 24px;
    background: #7b1220;
    color: #fff;
    font-size: .9rem;
    font-weight: 600;
    font-family: inherit;
    border-radius: 8px;
    cursor: pointer;
    transition: background .2s;
    white-space: nowrap;
    user-select: none;
    line-height: 1;
}

.ucu-file-btn:hover { background: #60101b; }

.ucu-file-name {
    font-size: .875rem;
    color: #6b7280;
    font-style: italic;
}

.ucu-file-name.ucu-has-file {
    color: #166534;
    font-style: normal;
    font-weight: 500;
}

/* ── Hints / Errors ───────────────────────────────────────────────────── */
.ucu-field__hint {
    font-size: .78rem;
    color: #6b7280;
}

.ucu-field__error {
    font-size: .78rem;
    color: #ef4444;
    min-height: 16px;
}

/* ── Алерти ───────────────────────────────────────────────────────────── */
.ucu-booking__notice {
    padding: 14px 18px;
    border-radius: 8px;
    font-size: .9rem;
    margin-bottom: 24px;
    line-height: 1.5;
}

.ucu-booking__notice--success {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #166534;
}

.ucu-booking__notice--error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #991b1b;
}

/* ── Навігація кроків ─────────────────────────────────────────────────── */
.ucu-step-nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 28px;
    padding-top: 20px;
    border-top: 1px solid #f3f4f6;
    gap: 12px;
}

.ucu-btn {
    padding: 12px 32px;
    font-size: .95rem;
    font-weight: 600;
    font-family: inherit;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background .2s, transform .1s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 140px;
    flex: 1;
    max-width: 340px;
}

.ucu-btn-next,
.ucu-btn-submit {
    background: #7b1220;
    color: #fff;
}

.ucu-btn-next:hover:not(:disabled),
.ucu-btn-submit:hover:not(:disabled) {
    background: #60101b;
}

.ucu-btn-prev {
    background: #6b7280;
    color: #fff;
}

.ucu-btn-prev:hover:not(:disabled) {
    background: #4b5563;
}

.ucu-btn:active:not(:disabled) { transform: scale(.98); }
.ucu-btn:disabled { opacity: .65; cursor: not-allowed; }

/* ── Адаптив ──────────────────────────────────────────────────────────── */
@media (max-width: 680px) {
    .ucu-booking__grid { grid-template-columns: 1fr; }
    .ucu-stepper { gap: 0; }
    .ucu-step { min-width: 48px; max-width: 64px; }
    .ucu-step-label { font-size: .6rem; }
    .ucu-step-circle { width: 26px; height: 26px; font-size: .7rem; }
    .ucu-step-line { margin-top: 12px; min-width: 4px; }
    .ucu-btn { padding: 11px 16px; min-width: 0; font-size: .88rem; }
    .ucu-step-nav { flex-wrap: wrap; }
    .ucu-date-row { grid-template-columns: 1fr 1fr 1fr; gap: 6px; }
}

/* ── Зірочка обов'язкового поля ──────────────────────────────────────── */
.ucu-req { color: #ef4444; margin-left: 2px; }

/* ── Степер завжди під заголовком ────────────────────────────────────── */
.ucu-booking__title { margin: 0 0 24px; }
.ucu-stepper { margin-bottom: 32px; }

/* ── Соціальні мережі — поле під чекбоксом ───────────────────────────── */
.ucu-social-input {
    margin: 6px 0 10px 24px;
}

.ucu-social-input input {
    width: 100%;
    box-sizing: border-box;
    padding: 9px 14px;
    font-size: .9rem;
    font-family: inherit;
    border: 1.5px solid #d1d5db;
    border-radius: 8px;
    outline: none;
    color: #111827;
    background: #f9fafb;
    transition: border-color .2s, box-shadow .2s;
}

.ucu-social-input input:focus {
    border-color: #4caf50;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(76,175,80,.12);
}
