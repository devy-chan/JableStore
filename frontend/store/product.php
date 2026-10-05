<?php
$productId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($productId <= 0) { header('Location: index.php'); exit; }
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Furniture Details | JABLE STORE</title><link rel="icon" type="image/png" href="../../logo5.png">
<link rel="stylesheet" href="assets/store.css"></head>
<body>
<div class="announcement"><div class="container announcement-inner"><span>Clear pricing, real stock counts and an easy checkout — every visit to JABLE.</span><span>Furniture made for Filipino homes</span></div></div><header class="site-header"><div class="container nav"><a class="brand" href="index.php"><span class="brand-mark">J</span> JABLE STORE</a><nav class="main-nav"><a href="index.php">Home</a><a class="active" href="index.php#products">Shop</a></nav><a class="cart-link" href="cart.php">🛒 <span>Cart</span><strong id="cartCount">0</strong></a></div></header>
<main class="container page-space"><a class="continue" style="text-align:left;margin:0 0 20px" href="index.php#products">← Back to furniture</a><div id="detail"></div></main>
<script>
const productId=<?= $productId ?>;let cart=JSON.parse(localStorage.getItem('storeCart')||'[]');
const money=v=>new Intl.NumberFormat('en-PH',{style:'currency',currency:'PHP'}).format(Number(v)||0);
const esc=v=>String(v??'').replace(/[&<>'"]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#039;','"':'&quot;'}[c]));
function count(){document.getElementById('cartCount').textContent=cart.reduce((n,i)=>n+i.quantity,0)}
fetch('../../backend/actions/storeProducts.php').then(r=>r.json()).then(d=>{
 const p=(d.data||[]).find(x=>x.id===productId);if(!p)throw Error('Product not found.');
 document.getElementById('detail').innerHTML=`<div class="detail-card"><div class="detail-image">${p.image ? `<img src="${esc(p.image)}" alt="${esc(p.name)}" onerror="this.style.display='none';this.parentElement.classList.add('no-image')">` : `<div class="no-product-image"><span>NO IMAGE</span><small>Add a product photo from Admin → Add Product</small></div>`}</div><div class="detail-info"><div class="category">${esc(p.category)}</div><h1>${esc(p.name)}</h1><div class="brand-name">Material: ${esc(p.brand)}</div><p class="detail-description">${esc(p.description||'A practical furniture piece designed for everyday comfort and style.')}</p><div class="detail-price">${money(p.price)}</div><div class="detail-facts"><div><span>Availability</span><b>✓ ${p.quantity} in stock</b></div><div><span>Category</span><b>${esc(p.category)}</b></div><div><span>Shopping</span><b>Add to cart anytime</b></div></div><div class="detail-note"><b>Before you order</b><span>Review the product photo, description, price and available quantity above. Your cart can be updated anytime before checkout.</span></div><button class="detail-btn" id="add">Add to cart</button><a class="secondary-btn" style="margin-top:10px" href="cart.php">View cart</a></div></div>`;
 document.getElementById('add').onclick=()=>{let f=cart.find(i=>i.id===p.id);if(f)f.quantity++;else cart.push({id:p.id,name:p.name,price:p.price,quantity:1,image:p.image});localStorage.setItem('storeCart',JSON.stringify(cart));count();document.getElementById('add').textContent='Added to cart ✓';};
}).catch(e=>document.getElementById('detail').innerHTML='<div class="state error">'+esc(e.message)+'</div>');count();
</script></body></html>