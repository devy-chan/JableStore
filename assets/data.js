const JABLE_PRODUCTS = [
{id:1,name:'Modern Dining Table',brand:'JABLE Home',category:'Dining',price:8999,quantity:12,image:'images/products/2696613376a961994273ec.png',description:'A warm modern dining table designed for everyday family meals and gatherings.'},
{id:2,name:'Classic Upholstered Bed',brand:'JABLE Home',category:'Bedroom',price:15999,quantity:8,image:'images/products/4576240986a9618ec1ec96.png',description:'A comfortable upholstered bed with a clean, timeless look for the bedroom.'},
{id:3,name:'Comfort Accent Chair',brand:'JABLE Home',category:'Living Room',price:6499,quantity:10,image:'images/products/8458245706a961959412e1.png',description:'A soft accent chair that adds comfort and a refined touch to everyday spaces.'},
{id:4,name:'Minimal Console Table',brand:'JABLE Home',category:'Storage',price:5299,quantity:7,image:'images/products/20883693776a9619f481cb7.png',description:'A compact console table for entryways, living rooms, and display spaces.'},
{id:5,name:'Wood Dining Set',brand:'JABLE Home',category:'Dining',price:12499,quantity:6,image:'images/products/5282016326a961a25a2103.png',description:'A practical dining set designed for shared meals and comfortable everyday use.'},
{id:6,name:'Family Sofa',brand:'JABLE Home',category:'Living Room',price:18999,quantity:5,image:'images/products/4909253296a961924910c2.png',description:'A spacious sofa made for relaxing, family time, and welcoming guests.'}
];
const JABLE_CATEGORIES=[...new Set(JABLE_PRODUCTS.map(p=>p.category))].map((name,i)=>({id:i+1,name}));
