IMG_DATA_FILEPATH = 'imgdata.txt'

"""
Generates data for the first 120 drawing tasks
"""

with open(IMG_DATA_FILEPATH, 'w') as outfile:
    for i in range(4,5):
        for j in range(1,16):
            baseImgId = str(j)
            if len(baseImgId) == 1:
                baseImgId = '0' + baseImgId        
            imgId = str(i) + '1' + baseImgId
            imgData = imgId + ' False 0 -1 \n'
            outfile.write(imgData)
