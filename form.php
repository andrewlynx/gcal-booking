.ucu-admin-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
    max-width: 1100px;
}

.ucu-admin-panel {
    background: #fff;
    border: 1px solid #c3c4c7;
    padding: 16px;
}

.ucu-admin-panel--wide {
    grid-column: 1 / -1;
}

.ucu-error-text {
    color: #b32d2e;
    white-space: pre-wrap;
}

.ucu-form-table textarea.large-text {
    min-height: 140px;
}

@media (max-width: 900px) {
    .ucu-admin-grid {
        grid-template-columns: 1fr;
    }
}
