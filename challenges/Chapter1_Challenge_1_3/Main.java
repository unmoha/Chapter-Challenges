import java.util.Random;
import java.util.Scanner;

public class Main {
    public static void main(String[] args) {
        // Initialize health and game objects
        int health = 100;
        Random rand = new Random();
        Scanner sc = new Scanner(System.in);

        // For loop for 5 rooms
        for (int room = 1; room <= 5; room++) {
            System.out.println("Entering room " + room + "...");
            // Random event: 1 (trap), 2 (potion), 3 (monster)
            int event = rand.nextInt(3) + 1;

            // Switch statement for event handling
            switch (event) {
                case 1:
                    health -= 20;
                    System.out.println("A trap! Health: " + health);
                    break;
                case 2:
                    health += 15;
                    if (health > 100) health = 100; // Cap at 100
                    System.out.println("Healing potion! Health: " + health);
                    break;
                case 3:
                    // Monster: random number 1-5
                    int monster = rand.nextInt(5) + 1;
                    int guess;
                    // Do-while loop for guessing
                    do {
                        System.out.print("Monster! Guess (1-5): ");
                        guess = sc.nextInt();
                    } while (guess != monster);
                    System.out.println("Monster defeated!");
                    break;
            }

            // Check for defeat
            if (health <= 0) {
                System.out.println("You have been defeated!");
                break;
            }
        }

        // Check for victory
        if (health > 0) {
            System.out.println("You cleared the dungeon! Final health: " + health);
        }
        sc.close();
    }
}