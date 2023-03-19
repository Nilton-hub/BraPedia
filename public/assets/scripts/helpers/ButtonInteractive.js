export default class ButtonInteractive
{
   construct(selector) {
       this._button = document.querySelector(selector);
       this._buttonContent = this._button.innerHTML;
   }

   get buttonContent() {
       return this._buttonContent;
   }

   get button() {
       return this._button;
   }

   disable() {
       this._button.setAttribute('disabled', '');
       this._button.innerHTML = `<span class="btn-load"></span> Aguarde...`;
   }

   enable() {
       this._button.removeAttribute('disabled');
       this._buttonContent.innerHTML = this._buttonContent;
   }
}
