# Fantasy Hockey
This application aims to manage a fantasy hockey league. Fantasy teams are created by choosing players to add to the team, and fantasy teams play matches against each other, where the winner is which team’s players performed
the best during a certain time period. 
The database will model the viewers and coaches, the real life hockey teams, the
players, the fantasy leagues and the fantasy teams, as well as the fantasy matches that get
played and the bets placed on the fantasy teams by viewers, as well as player equipment.

The application platform will be a website written in PHP, this will allow a good
simulation of real fantasy hockey platforms. It will also allow for interaction with the database.
We will also be using the MySQL database management software to manage the database
and data.

### SQL Queries
Implementing the webapp required us to use many different SQL queries:
* INSERT - Inserting a new user by creating an account
* DELETE - Deleting a team
* UPDATE - Change which fantasy team a player belongs to, for example in a trade
* SELECTION - Display all the information about the players in a fantasy team
* PROJECTION - Let the user choose which player attributes they want to see.
* JOIN - Many applications, like showing a list of all fantasy teams in the league with info about the owner ofeach
* GROUP BY - Used in a stat table, e.g. find all the players that have scored 5 goals
* HAVING - Find the average points by each player, where you filter for only players with an average of “x” points or higher
* Nested Aggregation with GROUP BY - Also in the stat table, find the AVG amount of goals in each team
* Division - Find which player(s) have played in every match a certain fantasy team has been involved in