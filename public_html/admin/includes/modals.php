<style>
  .modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: none;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}
.modal-overlay.show {
  display: flex;
}
.modal-box {
  background: #e2e2e2;
  padding: 2rem;
  border-radius: 1rem;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
  max-width: 400px;
  width: 90%;
  text-align: center;
  animation: fadeIn 0.25s ease;
}
.modal-box p {
  font-size: 1rem;
  margin-bottom: 1.5rem;
  color: #333;
}
.modal-actions button {
  padding: 0.5rem 1.25rem;
  border: none;
  border-radius: 0.5rem;
  font-weight: bold;
  cursor: pointer;
  margin: 0 0.5rem;
  transition: background 0.2s ease;
}

.btn-confirm {
  background-color: #dfd898;
  color: black;
}
.btn-confirm:hover {
  background-color: #dacf70;
}

.btn-cancel {
  background-color: #dadadaff;
  color: black;
}
.btn-cancel:hover {
  background-color: #b2b2b2ff;
}

/* Fade-in animation */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}
</style>

<template id="confirmModal">
  <div class="modal-overlay">
    <div class="modal-box">
      <div class="modal-actions">
        <p></p>
        <button id="confirmYes" class="btn-confirm">Yes</button>
        <button id="confirmNo" class="btn-cancel">No</button>
      </div>
    </div>
  </div>
</template>

<template id="alertModal">
  <div class="modal-overlay">
    <div class="modal-box">
      <div class="modal-actions">
        <p></p>
        <button id="confirmOk" class="btn-confirm">Ok</button>
      </div>
    </div>
  </div>
</template>