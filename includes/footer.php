</main>
<footer class="site-footer">
    <div class="container footer-wrap">
        <div><strong>Sugar Nest</strong><span>Crafted with love, shared from our kitchen to yours.</span></div>
        <span>&copy; <?= date('Y') ?> Sugar Nest</span>
    </div>
</footer>

<div class="confirm-modal" id="confirm-modal" hidden>
    <div class="confirm-modal-backdrop" data-close-confirm-modal></div>
    <div
        class="confirm-modal-card"
        role="dialog"
        aria-modal="true"
        aria-labelledby="confirm-modal-title"
        aria-describedby="confirm-modal-message"
    >
        <button
            class="confirm-modal-close"
            type="button"
            data-close-confirm-modal
            aria-label="Close confirmation dialog"
        >
            &times;
        </button>
        <p class="eyebrow">Please confirm</p>
        <h2 id="confirm-modal-title">Remove this item?</h2>
        <p id="confirm-modal-message" class="muted"></p>
        <div class="confirm-modal-actions">
            <button class="button danger-button" id="confirm-action" type="button">
                Delete
            </button>
            <button class="text-link" id="confirm-cancel" type="button" data-close-confirm-modal>
                Cancel
            </button>
        </div>
    </div>
</div>

<script src="<?= BASE_URL ?>/js/script.js"></script>
</body>
</html>
