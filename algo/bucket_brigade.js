/*Bucket brigade algorithm

Classifier Systems and the Bucket Brigade.

Holland [Holland, 1985] has proposed the meanwhile well-known bucket brigade algorithm for classifier systems. In this section we shortly review the main idea of this algorithm.
Messages in form of bitstrings of size $n$ can be placed on a global message list either by the environment or by entities called classifiers. Each classifier consists of a condition part and an action part defining a message it might send to the message list. Both parts are strings out of  $\{0,1,\_ \}^{n}$ where the `_' serves as a `don't care' if it appears in the condition part. (Less important for our purposes, the `_' serves as a `pass-through' if it appears in the action part.) A non-negative real number is associated with each classifier indicating its `strength'.
During one cycle all messages on the message list are compared with the condition parts of all classifiers of the system. Each matching classifier computes a `bid' by multiplying its specificity (the number of non-don't cares in its condition part) with the product of its strength and a small factor. The highest bidding classifiers may place their message on the message list of the next cycle, but they have to pay with their bid which is distributed among the classifiers active during the last time step which set up the triggering conditions (this explains the name bucket brigade).
Certain messages result in an action within the environment (like moving a robot one step). Because some of these actions may be regarded as 'useful' by an external critic who can give payoff by increasing the strengths of the currently active classifiers, learning may take place. The central idea is that classifiers which are not active when the environment gives payoff but which had an important role for setting the stage for directly rewarded classifiers can earn credit by participating in `bucket brigade chains'. The success of some active classifier recursively depends on the success of classifiers that are active at the following time ticks.
As an additional means for improving performance Holland introduces a genetic algorithm to construct new classifiers from old successful ones. This feature will not be important for our purposes.
*/

Class Classifier {

var specifity = 0;
var strength = 0;

function Classifier(){
	bid = specifity * strength
}

function Condition(){

}

function Action(){

}

}