EVAL_DATA_FILEPATH = 'evaldata.txt'
NUM_BASE_IMAGES = 10
NUM_GROUPS = 4

"""
Generates data file to keep track of drawing tasks
"""

with open(EVAL_DATA_FILEPATH, 'w') as outfile:
    for h in range(1,NUM_GROUPS+1):
        for i in range(1,NUM_GROUPS+1):
            if h >= i:
                continue
            for j in range(1,NUM_BASE_IMAGES+1):
                baseImgId = str(j)
                if len(baseImgId) == 1:
                    baseImgId = '0' + baseImgId        
                imgId1 = str(h) + str(h) + baseImgId
                imgId2 = str(i) + str(i) + baseImgId
                imgData = imgId1 + ' ' + imgId2 + ' 0 0 0 \n'
                outfile.write(imgData)
