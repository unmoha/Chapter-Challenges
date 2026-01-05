import java.io.IOException;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;
import java.util.Scanner;
import java.util.zip.ZipEntry;
import java.util.zip.ZipOutputStream;

public class Main {
    static Path dir = Paths.get("entries");

    public static void main(String[] args) throws IOException {
        if (!Files.exists(dir)) Files.createDirectory(dir);
        Scanner sc = new Scanner(System.in);

        System.out.println("1. Write Entry  2. Read Entries  3. Search  4. Backup");
        int choice = sc.nextInt(); sc.nextLine();

        if (choice == 1) {
            System.out.println("Write entry:");
            String text = sc.nextLine();
            String name = "diary_" +
                LocalDateTime.now().format(DateTimeFormatter.ofPattern("yyyy_MM_dd_HH_mm_ss")) + ".txt";
            Files.write(dir.resolve(name), text.getBytes());
        } else if (choice == 2) {
            Files.list(dir).forEach(System.out::println);
        } else if (choice == 3) {
            System.out.println("Search keyword:");
            String keyword = sc.nextLine();
            Files.list(dir).filter(p -> {
                try {
                    return Files.readString(p).contains(keyword);
                } catch (IOException e) { return false; }
            }).forEach(System.out::println);
        } else if (choice == 4) {
            try (ZipOutputStream zos = new ZipOutputStream(Files.newOutputStream(Paths.get("backup.zip")))) {
                Files.list(dir).forEach(p -> {
                    try {
                        zos.putNextEntry(new ZipEntry(p.getFileName().toString()));
                        Files.copy(p, zos);
                        zos.closeEntry();
                    } catch (IOException e) {}
                });
            }
        }
        sc.close();
    }
}