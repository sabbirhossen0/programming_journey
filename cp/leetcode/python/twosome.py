


# Given an array of integers nums and an integer target, return indices
# of the two numbers such that they add up to target.

# You may assume that each input would have exactly one solution, and you may not use the same element twice.

# You can return the answer in any order.

 

# Example 1:

# Input: nums = [2,7,11,15], target = 9
# Output: [0,1]
# Explanation: Because nums[0] + nums[1] == 9, we return [0, 1].
# Example 2:

# Input: nums = [3,2,4], target = 6
# Output: [1,2]




 
class Solution(object):
    def twoSum(self, nums, target):
        """
        :type nums: List[int]
        :type target: int
        :rtype: List[int]
        """
        # nums=[2,7,11,15]
        # target=9
        List=[]
        for x in range(0,len(nums)):
                
              if(nums[x]+nums[x+1]==target):
                  List.append(x)
                  List.append(x+1)
                  print(List)
                  return List


s = Solution()
s.twoSum([2,7,11,15], 9)