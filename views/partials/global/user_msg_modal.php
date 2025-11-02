<div class="modal fade" id="userMsgModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered z-2">
        <div class="modal-content">
            <div class="modal-header" id="userMsgModalHeader">
                <p class="modal-title fs-6" id="userMsgTitle"></p>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted" id="userMsgContent"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    const showUserMsgModal = ({ title, content, headerBgColor = 'bg-secondary', titleColor = 'text-dark' }) => {
        const userMsgModal = new bootstrap.Modal(document.getElementById('userMsgModal'));
        const userMsgModalHeader = document.getElementById('userMsgModalHeader');
        const userMsgContent = document.getElementById('userMsgContent');
        const userMsgTitle = document.getElementById('userMsgTitle');
    
        userMsgTitle.innerText = title;
        userMsgContent.innerText = content;
        userMsgModalHeader.classList.add(headerBgColor);
        userMsgTitle.classList.add(titleColor)
        userMsgModal.show();
    }
</script>