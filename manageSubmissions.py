import socket
import time

# number of seconds that image will be locked, 20 min
MAX_DRAWING_TIME = 72000 
IMG_DATA_FILEPATH = 'imgdata.txt'

class SubmissionImage:
    '''
    Creates a SubmissionImage which keeps track of the data relate to an image
    
    Parameters
        totalGroupSize - total number of people that need to work on the image to complete it
        currentGroupSize - number of people that have worked on the image so far
        locked - whether the image is available to be assigned
        timeStamp - most recent time the image was locked until
        userId - the id of the user that was most recently assigned this image
    '''
    def __init__(self, totalGroupSize, currentGroupSize, locked, timeStamp, userId):
        self.totalGroupSize = totalGroupSize
        self.currentGroupSize = currentGroupSize
        self.locked = locked
        self.timeStamp = timeStamp
        self.userId = userId

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
    Returns whether the image is available for assigning
    '''
    def isLocked(self):
        if self.locked:
            self.tryUnlockImage()
        return self.locked
    
    '''
    Marks an image with the given ID as complete (unavailable for drawing)
    '''
    def setFinished(self):
        self.locked = True
        self.timeStamp = float('inf')

    '''
    Keep track of the current user working on the image

    Parameter
        userId - id of the user
    '''
    def setUser(self, userId):
        self.userId = userId

class EvaluateTask:

    '''
    EvaluateTask keeps tracks of which pairwise comparisons have already been made 
    between images that share the same base image

    Parameters
        imgIds - a list of image ids that share the same base image
    '''
    def __init__(self, imgIds):
        self.imgIds = imgIds
        self.pairwiseComparisons = dict()
        for i in range(0, len(imgIds)):
            for j in range(i+1, len(imgIds)):
                id1 = imgIds[i]
                id2 = imgIds[j]
                self.pairwiseComparisons[(id1,id2)] = -1

    '''
    Returns the ids of two images that still need to be compared
    Returns -1,-1 if all comparisons are finished already     
    '''
    def getComparison(self):
        for comparison, result in self.pairwiseComparisons.iteritems():
            if result == -1:
                return comparison
        return -1, -1

                        
'''
A class that manages the state of submissions.
TODO: Maybe more functions are needed.
'''
class SubmissionManager:

    def __init__(self):
        # map from ids to SubmissionImage objects
        self.imageDict = dict()


    '''
    Returns the ID of an image that is currently available to be drawn on
    or -1 if there is no available image

    Parameters
        userId - id of the user
    '''
    def getAvailableImageToDrawOn(self, userId):
        availableImgId = -1
        minTimeStamp = float('inf')
        for imgId, img in self.imageDict.iteritems():
            if not img.isLocked() and img.timeStamp < minTimeStamp:
                minTimeStamp = img.timeStamp
                availableImgId = imgId
        if availableImgId != -1:
            self.imageDict[availableImgId].setUser(userId)
            self.imageDict[availableImgId].lockImage(MAX_DRAWING_TIME)
        return availableImgId
    
    '''
    Return the ID of two images that are currently available to be compared against each other

    Parameters
        userId - the id of the user
    '''
    def getImagesToCompare(self, userId):
        pass

    '''
    Returns a SubmissionImage object with the given image ID.
    '''
    def getSubmissionImage(self, imgId):
        return self.imageDict[imgId]
   
    '''
    Called when a drawing task is finished, creates a new image id if necessary and returns the mturk reward code
    '''
    def drawTaskFinishImage(self, userId, imgId):
        self.imageDict[imgId].setFinished()
        totalGroupSize = int(imgId[0])
        currentGroupSize = int(imgId[1])
        baseImgId = imgId[2:4]
        if currentGroupSize < totalGroupSize:
            nextImgId = str(totalGroupSize) + str(currentGroupSize+1) + baseImgId
            self.imageDict[nextImgId] = SubmissionImage(totalGroupSize, currentGroupSize+1, False, 0.0, -1)
        return 0 # how do i know what the mturk reward code is?

    '''
    Loads the contents of this object from disk.
    '''
    def load(self, filePath):
        with open(filePath) as infile:
            for line in infile:
                imgData = line.split()
                imgId = imgData[0]
                totalGroupSize = int(imgId[0])
                currentGroupSize = int(imgId[1])
                baseImgId = imgId[2:4]
                locked = imgData[1] == 'True'
                timeStamp = float(imgData[2])
                userId = imgData[3]
                img = SubmissionImage(totalGroupSize, currentGroupSize, locked, timeStamp, userId)
                self.imageDict[imgId] = img
    
    '''
    Saves the contents of this object to disk.
    '''
    def save(self, filePath):
        with open(filePath, 'w') as outfile:
            for imgId, img in self.imageDict.iteritems():
                imgData = imgId + ' ' + str(img.isLocked()) + ' ' + str(img.timeStamp) + ' ' + img.userId + ' \n'
                outfile.write(imgData)

myManager = SubmissionManager()
myManager.load(IMG_DATA_FILEPATH)
mySocket = socket.socket()
mySocket.bind(('127.0.0.1', 9876))
mySocket.listen(5)
while 1:
    connectedSocket, addr = mySocket.accept()
    received = connectedSocket.recv(1024).split(',')
    # TODO: use the contents of the string array "received"
    # to invoke the relevant functions in Submission Manager
    
    toSend = None
    if received[0] == 'drawTaskGetImage':
        toSend = myManager.getAvailableImageToDrawOn(received[1])
    elif received[0] == 'drawTaskFinishImage':
        toSend = myManager.drawTaskFinishImage(received[1], received[2])   

    print toSend
    connectedSocket.sendall(toSend)
    connectedSocket.close()
    myManager.save(IMG_DATA_FILEPATH)

