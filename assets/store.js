const API_URL = '';
const GITHUB_PAGES_MODE = true;
let cart = JSON.parse(localStorage.getItem('storeCart') || '[]');
let allProducts = [];
const $ = id => document.getElementById(id);
const money = v => new Intl.NumberFormat('en-PH',{style:'currency',currency:'PHP'}).format(Number(v)||0);
const esc = v => String(v ?? '').replace(/[&<>'"]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#039;','"':'&quot;'}[c]));
function saveCart(){localStorage.setItem('storeCart',JSON.stringify(cart));updateCartCount();}
function syncCartImages(products){const byId=new Map(products.map(p=>[Number(p.id),p.image||'']));let changed=false;cart.forEach(i=>{const next=byId.get(Number(i.id));if(next!==undefined&&i.image!==next){i.image=next;changed=true;}});if(changed)saveCart();}
function updateCartCount(){const e=$('cartCount');const side=$('sidebarCartCount');const n=cart.reduce((x,i)=>x+Number(i.quantity||0),0);if(e){e.textContent=n;e.classList.toggle('has-items',n>0);}if(side){side.textContent=n;side.classList.toggle('has-items',n>0);}}
function addToCart(product){const found=cart.find(i=>i.id===Number(product.id));if(found)found.quantity++;else cart.push({id:Number(product.id),name:product.name,price:Number(product.price),quantity:1,image:product.image});saveCart();showToast(`${product.name} added to your cart`);}
function showToast(text){const t=$('toast');if(!t)return;t.textContent='✓ '+text;t.classList.add('show');clearTimeout(window.toastTimer);window.toastTimer=setTimeout(()=>t.classList.remove('show'),2200);}
function furnitureImage(p){
  return String(p.image || '').trim();
}

function renderProducts(products){
  const grid=$('productGrid');
  if(!grid)return;
  grid.innerHTML='';
  $('emptyState')?.classList.toggle('hidden',products.length!==0);
  products.forEach((p,index)=>{
    const card=document.createElement('article');
    card.className='card reveal';
    card.style.setProperty('--delay',`${Math.min(index,8)*45}ms`);
    const stock=Number(p.quantity||0);
    const image=furnitureImage(p);
    p.image=image;
    const imageContent=image
      ? `<img src="${esc(image)}" alt="${esc(p.name)}" loading="lazy" onerror="this.closest('.image-wrap').classList.add('no-image');this.remove()">`
      : `<div class="no-product-image"><span>NO IMAGE</span><small>Add a product photo from Admin → Add Product</small></div>`;
    card.innerHTML=`<a class="card-image-link" href="product.html?id=${encodeURIComponent(p.id)}"><div class="image-wrap ${image?'':'no-image'}">${imageContent}<span class="stock-badge">${stock<=5?'Only '+stock+' left':'In stock'}</span><span class="view-label">View details →</span></div></a><div class="card-body"><div class="category">${esc(p.category)}</div><h3 title="${esc(p.name)}">${esc(p.name)}</h3><div class="brand-name">${esc(p.brand)}</div><p class="description">${esc(p.description||'A practical furniture piece designed for everyday comfort and style.')}</p><div class="product-meta"><span>✓ Ready to order</span><span>•</span><span>${stock} available</span></div><div class="card-bottom"><span class="price">${money(p.price)}</span><button class="add-btn" type="button">Add to cart</button></div></div>`;
    card.querySelector('.add-btn').onclick=e=>{e.preventDefault();addToCart(p)};
    grid.appendChild(card);
  });
  observeReveals();
}
function applyFiltersAndRender(){let list=allProducts.slice();const min=parseFloat($('priceMin')?.value);const max=parseFloat($('priceMax')?.value);if(!isNaN(min))list=list.filter(p=>Number(p.price)>=min);if(!isNaN(max))list=list.filter(p=>Number(p.price)<=max);const sort=$('sortSelect')?.value||'featured';if(sort==='price-asc')list.sort((a,b)=>Number(a.price)-Number(b.price));else if(sort==='price-desc')list.sort((a,b)=>Number(b.price)-Number(a.price));else if(sort==='name-asc')list.sort((a,b)=>String(a.name).localeCompare(b.name));else if(sort==='name-desc')list.sort((a,b)=>String(b.name).localeCompare(a.name));renderProducts(list);$('resultText').textContent=`${list.length} furniture item${list.length===1?'':'s'} ready to explore`;}
async function loadProducts(){
  const grid=$('productGrid'); if(!grid)return;
  const search=$('searchInput')?.value.trim().toLowerCase()||'';
  const active=document.querySelector('.sidebar-category.active'); const category=active?.dataset.category||'0';
  $('errorState')?.classList.add('hidden');
  $('resultText').textContent='Finding furniture...';
  let data=JABLE_PRODUCTS.slice();
  if(search)data=data.filter(p=>[p.name,p.brand,p.category,p.description].join(' ').toLowerCase().includes(search));
  if(category!=='0')data=data.filter(p=>String(JABLE_CATEGORIES.find(c=>c.id==category)?.name||'')===String(p.category));
  allProducts=data; syncCartImages(allProducts); applyFiltersAndRender();
}
async function loadCategories(){
  const sidebarRow=$('sidebarCategoryRow'), categoryRow=$('categoryRow');
  if(!sidebarRow&&!categoryRow)return;
  const selectedCategory=document.querySelector('.sidebar-category.active')?.dataset.category||'0';
  const categories=JABLE_CATEGORIES;
  const headerSelect=$('headerCategory');
  if(headerSelect){headerSelect.innerHTML='<option value="0">CATEGORIES</option>';categories.forEach(cat=>{const option=document.createElement('option');option.value=cat.id;option.textContent=cat.name;headerSelect.appendChild(option)});headerSelect.value=selectedCategory;}
  if(sidebarRow){sidebarRow.innerHTML='';const all=document.createElement('button');all.className='sidebar-category'+(selectedCategory==='0'?' active':'');all.dataset.category='0';all.type='button';all.innerHTML='<span>All Furniture</span><small>All</small>';all.onclick=()=>selectStoreCategory('0',all);sidebarRow.appendChild(all);categories.forEach(cat=>{const b=document.createElement('button');b.className='sidebar-category'+(String(cat.id)===String(selectedCategory)?' active':'');b.dataset.category=cat.id;b.type='button';b.innerHTML=`<span>${esc(cat.name)}</span><small>View</small>`;b.onclick=()=>selectStoreCategory(String(cat.id),b);sidebarRow.appendChild(b)});}
  if(categoryRow){categoryRow.innerHTML='';const all=document.createElement('button');all.className='filter-btn'+(selectedCategory==='0'?' active':'');all.dataset.category='0';all.type='button';all.textContent='All';all.onclick=()=>selectStoreCategory('0',all);categoryRow.appendChild(all);categories.forEach(cat=>{const b=document.createElement('button');b.className='filter-btn'+(String(cat.id)===String(selectedCategory)?' active':'');b.dataset.category=cat.id;b.type='button';b.textContent=cat.name;b.onclick=()=>selectStoreCategory(String(cat.id),b);categoryRow.appendChild(b)});}
}

function selectStoreCategory(categoryId,sourceButton){
  document.querySelectorAll('.sidebar-category').forEach(x=>x.classList.toggle('active',String(x.dataset.category)===String(categoryId)));
  document.querySelectorAll('.filter-btn').forEach(x=>x.classList.toggle('active',String(x.dataset.category)===String(categoryId)));
  if($('headerCategory'))$('headerCategory').value=String(categoryId);
  if(sourceButton)sourceButton.classList.add('active');
  loadProducts();
  if(document.body.classList.contains('sidebar-open'))closeSidebar();
  document.querySelector('#products')?.scrollIntoView({behavior:'smooth',block:'start'});
}

// Refresh the category menu periodically so a category added/activated in
// Admin → Categories can appear in an already-open storefront without a
// manual page refresh. The database remains the single source of truth.
let categoryRefreshTimer;
function startCategoryAutoRefresh(){
  clearInterval(categoryRefreshTimer);
  categoryRefreshTimer=setInterval(()=>loadCategories(),10000);
}

function observeReveals(){if(!('IntersectionObserver' in window)){document.querySelectorAll('.reveal').forEach(e=>e.classList.add('visible'));return;}if(window.revealObserver)window.revealObserver.disconnect();window.revealObserver=new IntersectionObserver(entries=>entries.forEach(entry=>{if(entry.isIntersecting){entry.target.classList.add('visible');window.revealObserver.unobserve(entry.target)}}),{threshold:.08});document.querySelectorAll('.reveal:not(.visible)').forEach(e=>window.revealObserver.observe(e));}
let timer;if($('searchInput'))$('searchInput').addEventListener('input',()=>{clearTimeout(timer);timer=setTimeout(loadProducts,250)});
function syncHeaderSearch(value){if($('searchInput')){$('searchInput').value=value;clearTimeout(window.headerSearchTimer);window.headerSearchTimer=setTimeout(loadProducts,220);}}
$('headerSearch')?.addEventListener('input',e=>syncHeaderSearch(e.target.value));
$('headerSearchBtn')?.addEventListener('click',()=>{syncHeaderSearch($('headerSearch')?.value||'');document.querySelector('#products')?.scrollIntoView({behavior:'smooth',block:'start'});});
$('headerCategory')?.addEventListener('change',e=>selectStoreCategory(e.target.value,e.target));
$('mobileHeaderSearch')?.addEventListener('input',e=>{if($('headerSearch'))$('headerSearch').value=e.target.value;syncHeaderSearch(e.target.value);});
$('sortSelect')?.addEventListener('change',applyFiltersAndRender);
$('applyPriceFilter')?.addEventListener('click',applyFiltersAndRender);
$('priceMin')?.addEventListener('keydown',e=>{if(e.key==='Enter')applyFiltersAndRender()});
$('priceMax')?.addEventListener('keydown',e=>{if(e.key==='Enter')applyFiltersAndRender()});
$('newsletterForm')?.addEventListener('submit',e=>{e.preventDefault();const email=$('newsletterEmail').value.trim();if(email){localStorage.setItem('jableNewsletter',email);e.target.reset();showToast('You are subscribed to JABLE updates')}});
$('menuBtn')?.addEventListener('click',()=>{$('mainNav')?.classList.toggle('open');$('menuBtn').textContent=$('mainNav')?.classList.contains('open')?'✕':'☰'});
document.querySelectorAll('.main-nav a').forEach(a=>a.addEventListener('click',()=>{$('mainNav')?.classList.remove('open');if($('menuBtn'))$('menuBtn').textContent='☰'}));
window.addEventListener('scroll',()=>{document.querySelector('.site-header')?.classList.toggle('is-scrolled',window.scrollY>10)});

function closeSidebar(){document.body.classList.remove('sidebar-open');$('storeSidebar')?.classList.remove('open');$('sidebarBackdrop')?.classList.remove('show');$('sidebarBtn')?.setAttribute('aria-expanded','false');$('sidebarBtn')?.setAttribute('aria-label','Open store menu');}
function openSidebar(){document.body.classList.add('sidebar-open');$('storeSidebar')?.classList.add('open');$('sidebarBackdrop')?.classList.add('show');$('sidebarBtn')?.setAttribute('aria-expanded','true');$('sidebarBtn')?.setAttribute('aria-label','Close store menu');}
$('sidebarBtn')?.addEventListener('click',()=>{$('storeSidebar')?.classList.contains('open')?closeSidebar():openSidebar();});
$('sidebarClose')?.addEventListener('click',closeSidebar);
$('sidebarBackdrop')?.addEventListener('click',closeSidebar);
document.querySelectorAll('.sidebar-link').forEach(a=>a.addEventListener('click',closeSidebar));
window.addEventListener('keydown',e=>{if(e.key==='Escape')closeSidebar();});
updateCartCount();loadCategories();loadProducts();observeReveals();
