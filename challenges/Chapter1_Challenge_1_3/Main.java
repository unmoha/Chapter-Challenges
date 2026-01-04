import java.util.Random;
import java.util.Scanner;

public class Main {
    public static void main(String[] args) {
        int health = 100;
        Random rand = new Random();
        Scanner sc = new Scanner(System.in);

        for (int room = 1; room <= 5; room++) {
            System.out.println("Entering room " + room + "...");
            int event = rand.nextInt(3) + 1;

            switch (event) {
                case 1:
                    health -= 20;
                    System.out.println("A trap! Health: " + health);
                    break;
                case 2:
                    health += 15;
                    if (health > 100) health = 100;
                    System.out.println("Healing potion! Health: " + health);
                    break;
                case 3:
                    int monster = rand.nextInt(5) + 1;
                    int guess;
                    do {
                        System.out.print("Monster! Guess (1-5): ");
                        guess = sc.nextInt();
                    } while (guess != monster);
                    System.out.println("Monster defeated!");
                    break;
            }

            if (health <= 0) {
                System.out.println("You have been defeated!");
                break;
            }
        }

        if (health > 0) {
            System.out.println("You cleared the dungeon! Final health: " + health);
        }
        sc.close();
    }
}