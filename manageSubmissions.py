import socket

'''
'''
class SubmissionImage:
    '''
    Locks an image with the given ID (this image can't be given to another
    turker) until the number of seconds provided elapses
    '''
    def lockImage(self, numSeconds):
        pass
    
    '''
    Unlocks an image with the given ID
    '''
    def unlockImage(self):
        pass
    
    '''
    Marks an image with the given ID as complete (unavailable for drawing,
    available for evaluations)
    '''
    def completeImage(self):
        pass

'''
A class that manages the state of submissions.
TODO: Maybe more functions are needed.
'''
class SubmissionManager:
    def __init__(self):
        # map from IDs to SubmissionImage objects
        # self.imageMap = map()
        pass
    
    '''
    Given a user ID and group size, return the ID of an image that is currently
    available to be drawn on.
    '''
    def getAvailableImageToDrawOn(self, groupSize):
        return 1000
    
    '''
    Given a user ID and group size, return the ID of two images that are
    currently available to be compared against each other.
    '''
    def getAvailableImagesToEvaluate(self):
        return 1000, 1001
    
    '''
    Returns a SubmissionImage object with the given image ID.
    '''
    def getSubmissionImage(self, ID):
        return self.imageMap[ID]
    
    '''
    Loads the contents of this object from disk.
    '''
    def load(self, filePath):
        pass
    
    '''
    Saves the contents of this object to disk.
    '''
    def save(self, filePath):
        pass

myManager = SubmissionManager()
myManager.load('output.txt')
mySocket = socket.socket()
mySocket.bind(('127.0.0.1', 9876))
mySocket.listen(5)
while 1:
    connectedSocket, addr = mySocket.accept()
    received = connectedSocket.recv(1024)
    print received
    # TODO: use the contents of the string array "received"
    # to invoke the relevant functions in Submission MAnager
    toSend = "1001"
    connectedSocket.sendall(toSend)
    connectedSocket.close()
    myManager.save('output.txt')
