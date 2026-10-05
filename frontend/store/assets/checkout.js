let cart=JSON.parse(localStorage.getItem('storeCart')||'[]');
const money=v=>new Intl.NumberFormat('en-PH',{style:'currency',currency:'PHP'}).format(Number(v)||0);
const esc=v=>String(v??'').replace(/[&<>'"]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#039;','"':'&quot;'}[c]));
const root=document.getElementById('checkoutPage');
function render(){
 if(!cart.length){root.innerHTML='<div class="empty-cart"><h2>Your cart is empty</h2><p>Add furniture before checking out.</p><a class="primary-btn" href="index.php#products">Browse products</a></div>';return;}
 const total=cart.reduce((n,i)=>n+i.price*i.quantity,0);
 root.innerHTML=`<div class="checkout-layout"><form class="checkout-form" id="orderForm"><h2>Customer information</h2>
 <div class="form-grid"><label>Full name<input required name="name" placeholder="Juan Dela Cruz"></label><label>Phone number<input required name="phone" type="tel" placeholder="09XXXXXXXXX"></label></div>
 <label>Email address<input required name="email" type="email" placeholder="you@example.com"></label><label>Delivery address<textarea required name="address" rows="4" placeholder="House no., street, barangay, city, province"></textarea></label>
 <h2>Payment method</h2><div class="payment-options"><label><input type="radio" name="payment" value="Cash on Delivery" checked> Cash on Delivery</label><label><input type="radio" name="payment" value="GCash"> GCash</label><label><input type="radio" name="payment" value="Bank Transfer"> Bank Transfer</label></div>
 <label class="check"><input type="checkbox" required> I confirm that my order details are correct.</label><button class="primary-btn full" type="submit">Place order • ${money(total)}</button></form>
 <aside class="summary checkout-summary"><h2>Your order</h2>${cart.map(i=>`<div class="mini-item"><span>${esc(i.name)} <small>× ${i.quantity}</small></span><b>${money(i.price*i.quantity)}</b></div>`).join('')}<hr><div class="summary-total"><span>Total</span><strong>${money(total)}</strong></div><p class="secure-note">🔒 Your cart is stored in your browser until you place the order.</p></aside></div>`;
 document.getElementById('orderForm').onsubmit=e=>{e.preventDefault();const id='JBL-'+Date.now().toString().slice(-7);localStorage.removeItem('storeCart');localStorage.setItem('lastOrder',JSON.stringify({id,name:new FormData(e.target).get('name'),total}));location.href='success.php?order='+id};
}
render();
