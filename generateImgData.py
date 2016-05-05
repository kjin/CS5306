IMG_DATA_FILEPATH = 'imgdata.txt'

with open(IMG_DATA_FILEPATH, 'w') as outfile:
    for i in range(1,5):
        for j in range(1,31):
            baseImgId = str(j)
            if len(baseImgId) == 1:
                baseImgId = '0' + baseImgId        
            imgId = str(i) + '0' + baseImgId
            imgData = imgId + ' False 0 -1 \n'
            outfile.write(imgData)
