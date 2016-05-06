mkdir -p files
for i in {1..9}
do
    cp base_img/base$i.png files/100$i.png
    cp base_img/base$i.png files/200$i.png
    cp base_img/base$i.png files/300$i.png
    cp base_img/base$i.png files/400$i.png
done
for i in {10..30}
do
    cp base_img/base$i.png files/10$i.png
    cp base_img/base$i.png files/20$i.png
    cp base_img/base$i.png files/30$i.png
    cp base_img/base$i.png files/40$i.png
done