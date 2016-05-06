import socket
import time
import hashlib

# number of seconds that image will be locked, 60 min
MAX_DRAWING_TIME = 3600 
IMG_DATA_FILEPATH = 'imgdata.txt'
EVAL_DATA_FILEPATH = 'evaldata.txt'
NO_IMAGE = '-1'
NO_USER = '-1'
REWARD_CODE_STRING = 'fantasycreature'

class SubmissionImage:
    '''
    Creates a SubmissionImage which keeps track of the data related to an image
    
    Parameters
        totalGroupSize - total number of people that need to work on the image to complete it
        currentGroupSize - number of people that have worked on the image so far
        locked - whether the image is available to be assigned
        timeStamp - most recent time the image was locked until
        userId - the id of the user that was most recently assigned this image
        dataString - string containing information needed for loading
    '''
    def __init__(self, imgId=NO_IMAGE, locked=False, timeStamp=0, userId=NO_USER, finished=False, drawingSkill=0, dataString=None):
        if dataString is not None:
            self.loadData(dataString)
        else:
            self.imgId = imgId        
            self.locked = locked
            self.timeStamp = timeStamp
            self.userId = userId
            self.finished = finished
            self.drawingSkill = drawingSkill
            if imgId == NO_IMAGE:
                self.totalGroupSize = 0
                self.currentGroupSize = 0
            else:
                self.totalGroupSize = int(imgId[0])
                self.currentGroupSize = int(imgId[1])
            

    '''
    Locks an image with the given ID (this image can't be given to another
    turker) 

    Parameters
        numSeconds - number of seconds image will be locked
    '''
    def lockImage(self, numSeconds):
        currentTime = time.time()
        self.timeStamp = currentTime + numSeconds
        self.locked = True
    
    '''
    Checks the timeStamp of the image and tries to unlock it
    '''
    def tryUnlockImage(self):
        currentTime = time.time()
        if self.timeStamp < currentTime:
            self.locked = False
    
    '''
    Unlocks image immediately
    '''
    def unlockImage(self):
        self.locked = False
        self.timeStamp = 0.0

    '''
    Returns whether the image is currently being held by a user
    '''
    def isLocked(self):
        if self.locked:
            self.tryUnlockImage()
        return self.locked

    '''
    Returns whether the image is available for assigning
    Takes into account whether the image is being held by another user as well as already being finished 
    '''
    def isAvailable(self):
        return not self.finished and not self.isLocked()

    '''
    Returns state as a string
    '''                 
    def getData(self):
        data = self.imgId + ' ' + str(self.isLocked()) + ' ' + str(self.timeStamp) + ' ' + self.userId + ' ' + str(self.finished) + ' ' + str(self.drawingSkill)
        return data       

    '''
    Loads the given string
    ''' 
    def loadData(self, dataString):         
        data = dataString.split()
        self.imgId = data[0]
        self.locked = data[1] == 'True'
        self.timeStamp = float(data[2])
        self.userId = data[3]
        self.finished = data[4] == 'True'
        self.drawingSkill = int(data[5])
        if self.imgId == NO_IMAGE:
            self.totalGroupSize = 0
            self.currentGroupSize = 0
        else:
            self.totalGroupSize = int(self.imgId[0])
            self.currentGroupSize = int(self.imgId[1])


class EvalPair:

    '''
    EvalPair keeps tracks evaluations made for two images that share the same base image

    Parameters
        imgId1 - the id of one of the images
        imgId2 - the id of the other image
        score1 - number of times that imgId1 was preferred
        score2 - number of times the imgId2 was preferred
        numAssigned - number of times this pair has been assigned but not yet completed
    '''
    def __init__(self, imgId1=NO_IMAGE, imgId2=NO_IMAGE, score1=0, score2=0, numAssigned=0, dataString=None):
        if dataString is not None:
            self.loadData(dataString)
        else:
            self.baseImgId = imgId1[2:4]
            self.imgId1 = imgId1
            self.imgId2 = imgId2 
            self.score1 = score1
            self.score2 = score2
            self.numAssigned = numAssigned
    
    '''
    Returns approximately how many evaluations this pair has
    '''    
    def getNumEval(self):
        return self.score1 + self.score2 + self.numAssigned

    '''
    Returns state as a string
    '''                 
    def getData(self):
        dataString = self.imgId1 + ' ' + self.imgId2 + ' ' + str(self.score1) + ' ' + str(self.score2) + ' ' + str(self.numAssigned)
        return dataString 
       
    '''
    Loads the given string
    ''' 
    def loadData(self, dataString):
        data = dataString.split()
        self.baseImgId = data[0][2:4]
        self.imgId1 = data[0]
        self.imgId2 = data[1]
        self.score1 = int(data[2])
        self.score2 = int(data[3])
        self.numAssigned = int(data[4])
        
'''
A class that manages the state of submissions.
'''
class SubmissionManager:

    def __init__(self):
        # maps image ids to SubmissionImage objects
        self.imageDict = dict()
        # maps base image ids to lists of EvalPair objects
        self.evalPairDict = dict()

    '''
    Returns the ID of an image that is currently available to be drawn on
    or NO_IMAGE if there is no available image
    '''
    def getAvailableImageToDrawOn(self, userId):
        availableImgId = NO_IMAGE
        minTimeStamp = float('inf')
        for imgId, img in self.imageDict.iteritems():
            if img.isAvailable() and img.timeStamp < minTimeStamp:
                minTimeStamp = img.timeStamp
                availableImgId = imgId
        if availableImgId != NO_IMAGE:
            self.imageDict[availableImgId].userId = userId
            self.imageDict[availableImgId].lockImage(MAX_DRAWING_TIME)
        return availableImgId
    
    '''
    Returns the string containing the image ids of the specified number of eval pairs
    numEvalPairsReturned is the maximum number of pairs returned, less may be returned if there is not enough
    '''
    def getImagesToCompare(self, userId, numEvalPairsReturned):
        # Find the ten EvalPairs with the least evaluations
        alreadySelected = list()
        imageIds = list()        
        for i in range(0, numEvalPairsReturned):
            minEvalPair = None
            minNumEval = float('inf')        
            for pairIds, pair in self.evalPairDict.iteritems():
                if pair in alreadySelected:
                    continue
                if pair.getNumEval() < minNumEval:
                    minEvalPair = pair
                    minNumEval = pair.getNumEval()
            if minEvalPair is not None:
                minEvalPair.numAssigned += 1
                alreadySelected.append(minEvalPair)
                imageIds.append(minEvalPair.imgId1)
                imageIds.append(minEvalPair.imgId2)
        # Return a string of image ids separated by commas
        imageIdsString = ','
        imageIdsString = imageIdsString.join(imageIds)
        return imageIdsString

    '''
    Called when a drawing task is finished, creates a new image id if necessary and returns the mturk reward code
    '''
    def drawTaskFinishImage(self, userId, imgId, drawingSkill):
        self.imageDict[imgId].finished = True
        self.imageDict[imgId].unlockImage()
        self.imageDict[imgId].drawingSkill = drawingSkill
        totalGroupSize = int(imgId[0])
        currentGroupSize = int(imgId[1])
        baseImgId = imgId[2:4]
        # create a new image id if the image is not completed yet
        if currentGroupSize < totalGroupSize:
            nextImgId = str(totalGroupSize) + str(currentGroupSize+1) + baseImgId
            self.imageDict[nextImgId] = SubmissionImage(nextImgId)
        # create new evaluation tasks if group is finished with image
        elif currentGroupSize == totalGroupSize:
            for imgId2, img2 in self.imageDict.iteritems():
                totalGroupSize2 = int(imgId2[0])
                currentGroupSize2 = int(imgId2[1])
                baseImgId2 = imgId2[2:4]
                if imgId == imgId2 or totalGroupSize2 != currentGroupSize2 or baseImgId != baseImgId2:
                    continue
                if not (imgId,imgId2) in self.evalPairDict and not (imgId2,imgId) in self.evalPairDict:
                    self.evalPairDict[(imgId, imgId2)] = EvalPair(imgId, imgId2)        
        return self.getRewardCode(userId)

    '''
    Returns the reward code for the MTurker, which is a hash of their user id
    '''
    def getRewardCode(self, userId):
        m = hashlib.md5()
        m.update(userId + REWARD_CODE_STRING)
        rewardCode = m.hexdigest()   
        return userId + '_' + rewardCode
    
    '''
    Records the results of the pairwise evaluation
    imgId1 is preferred over imgID2
    '''
    def reportEvalResult(self, userId, imgId1, imgId2):
        # figure out the order of the key
        if (imgId1, imgId2) in self.evalPairDict:        
            pair = self.evalPairDict[(imgId1, imgId2)]
            pair.score1 += 1
            pair.numAssigned -= 1
        else:
            pair = self.evalPairDict[(imgId2, imgId1)]
            pair.score2 += 1
            pair.numAssigned -= 1
        return ''
    
    '''
    Immediately unlocks the image the user was assigned to work on
    '''
    def cancelDrawingTask(self, userId):
        for imgId, img in self.imageDict.iteritems():
            if img.userId == userId:
                img.unlockImage()
        return ''
        
    '''
    Loads the contents of this object from disk.
    '''
    def load(self, imgDataPath, evalDataPath):
        # load submission images
        with open(imgDataPath) as infile:
            for line in infile:
                img = SubmissionImage(dataString=line)
                self.imageDict[img.imgId] = img
        # load evaluation tasks
        with open(evalDataPath) as infile:
            for line in infile:
                pair = EvalPair(dataString=line)
                self.evalPairDict[(pair.imgId1, pair.imgId2)] = pair
    
    '''
    Saves the contents of this object to disk.
    '''
    def save(self, imgDataPath, evalDataPath):
        # save state of submission images
        with open(imgDataPath, 'w') as outfile:
            for imgId, img in self.imageDict.iteritems():
                outfile.write(img.getData() + ' \n')
        # save state of evaluation tasks
        with open(evalDataPath, 'w') as outfile:
            for pairIds, pair in self.evalPairDict.iteritems():
                outfile.write(pair.getData() + '\n')


myManager = SubmissionManager()
myManager.load(IMG_DATA_FILEPATH, EVAL_DATA_FILEPATH)
mySocket = socket.socket()
mySocket.bind(('127.0.0.1', 9876))
mySocket.listen(5)
while 1:
    connectedSocket, addr = mySocket.accept()
    received = connectedSocket.recv(1024).split(',')

    # Invoke the relevant functions in Submission Manager
    toSend = None
    if received[0] == 'drawTaskGetImage':
        toSend = myManager.getAvailableImageToDrawOn(received[1])
    elif received[0] == 'drawTaskFinishImage':
        toSend = myManager.drawTaskFinishImage(received[1], received[2], received[3])
    elif received[0] == 'evalTaskGetImages':
        toSend = myManager.getImagesToCompare(received[1], int(received[2]))
    elif received[0] == 'evalTaskCompare':
        toSend = myManager.reportEvalResult(received[1], received[2], received[3])
    elif received[0] == 'evalTaskFinish':
        toSend = myManager.getRewardCode(received[1])
    elif received[0] == 'drawTaskCancelImage':
        toSend = myManager.cancelDrawingTask(received[1])
    print toSend
    connectedSocket.sendall(toSend)
    connectedSocket.close()

    for imgId, img in myManager.imageDict.iteritems():
        img.tryUnlockImage()

    myManager.save(IMG_DATA_FILEPATH, EVAL_DATA_FILEPATH)