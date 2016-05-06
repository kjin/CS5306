IMG_DATA_FILEPATH = 'imgdata.txt'
NUM_BASE_IMAGES = 10
NUM_GROUPS = 4

"""
Generates data file to keep track of drawing tasks
"""

with open(IMG_DATA_FILEPATH, 'w') as outfile:
    for i in range(1,NUM_GROUPS+1):
        for j in range(1,NUM_BASE_IMAGES+1):
            baseImgId = str(j)
            if len(baseImgId) == 1:
                baseImgId = '0' + baseImgId        
            imgId = str(i) + '1' + baseImgId
            imgData = imgId + ' False 0 -1 False 0 \n'
            outfile.write(imgData)
