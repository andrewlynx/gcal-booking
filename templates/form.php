<div class="gcal-booking-wrap">
    <h2 class="gcal-booking-title">Записатись на зустріч</h2>

    <div class="gcal-booking-alert gcal-booking-success" style="display:none;"></div>
    <div class="gcal-booking-alert gcal-booking-error"   style="display:none;"></div>

    <form id="gcal-booking-form" novalidate>
        <div class="gcal-field">
            <label for="gcal-name">Прізвище <span class="gcal-required">*</span></label>
            <input
                type="text"
                id="gcal-name"
                name="name"
                placeholder="Прізвище"
                autocomplete="name"
                required
            />
            <span class="gcal-field-error"></span>
        </div> 
        <div class="gcal-field">
            <label for="gcal-name">Ім'я  <span class="gcal-required">*</span></label>
            <input
                type="text"
                id="gcal-name"
                name="name"
                placeholder="Ім'я"
                autocomplete="name"
                required
            />
            <span class="gcal-field-error"></span>
        </div>

        <div class="gcal-field">
            <label for="gcal-phone">Номер телефону <span class="gcal-required">*</span></label>
            <input
                type="tel"
                id="gcal-phone"
                name="phone"
                placeholder="+380 XX XXX XX XX"
                autocomplete="tel"
                required
            />
            <span class="gcal-field-error"></span>
        </div>

        <div class="gcal-booking-row">
            <div class="gcal-field">
                <label for="gcal-date">Дата <span class="gcal-required">*</span></label>
                <input
                    type="date"
                    id="gcal-date"
                    name="date"
                    required
                />
                <span class="gcal-field-error"></span>
            </div>

            <div class="gcal-field">
                <label for="gcal-time">Час <span class="gcal-required">*</span></label>
                <input
                    type="time"
                    id="gcal-time"
                    name="time"
                    required
                />
                <span class="gcal-field-error"></span>
            </div>
        </div>

        <button type="submit" class="gcal-submit-btn" id="gcal-submit">
            <span class="gcal-btn-text">Записатись</span>
            <span class="gcal-btn-loader" style="display:none;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-dasharray="31.4 31.4" transform="rotate(-90 12 12)">
                        <animateTransform attributeName="transform" type="rotate" from="0 12 12" to="360 12 12" dur="0.8s" repeatCount="indefinite"/>
                    </circle>
                </svg>
                Надсилання...
            </span>
        </button>
    </form>
</div>
