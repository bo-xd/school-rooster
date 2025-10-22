using MySql.Data.MySqlClient;
using Mysqlx.Crud;
using System;
using System.Collections.Generic;
using System.Data;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Data;
using System.Windows.Documents;
using System.Windows.Input;
using System.Windows.Media;
using System.Windows.Media.Imaging;
using System.Windows.Navigation;
using System.Windows.Shapes;

namespace WpfRoosterMaker
{
    /// <summary>
    /// Interaction logic for MainWindow.xaml
    /// </summary>
    public partial class MainWindow : Window
    {
        public MainWindow()
        {
            InitializeComponent();
            LoadAgenda();
        }

        public static DataTable dt_klassen = new DataTable();
        public static ListBoxItem SelectedListBox = new ListBoxItem();
        public static DateTime SelectedWeek = new DateTime();
        public static string SelectedKlas = "";

        List<ListBox> lists = new List<ListBox>();

        public static MySqlConnection Connect()
        {
            string connectionstring = "SERVER=localhost;DATABASE=roosterprojecttest;UID=root;PASSWORD=";
            try
            {
                MySqlConnection cnct = new MySqlConnection(connectionstring);
                return cnct;
            }
            catch (Exception ex)
            {
                MessageBox.Show(ex.ToString());
                return null;
            }

        }


        private void btnBackward_Click(object sender, RoutedEventArgs e)
        {
            ChangeDate(-1);
        }

        private void btnForward_Click(object sender, RoutedEventArgs e)
        {
            ChangeDate(1);
        }

        private void LoadAgenda()
        {
            dpWeek.SelectedDate = DateTime.Now;
            lists.Add(lb1);
            lists.Add(lb2);
            lists.Add(lb3);
            lists.Add(lb4);
            lists.Add(lb5);

            MySqlConnection connection = Connect();
            MySqlCommand cmd = new MySqlCommand("SELECT `klas` FROM `klassen` ORDER BY `klas` ASC", connection);
            connection.Open();
            dt_klassen.Load(cmd.ExecuteReader());
            connection.Close();
            Console.WriteLine("test1");
            foreach (DataRow row in MainWindow.dt_klassen.Rows)
            {
                Console.WriteLine("test2");
                cbKlas.Items.Add(row["klas"]);
            }
            cbKlas.SelectedIndex = 0;
        }

        private void ChangeDate(int index)
        {
            dpWeek.SelectedDate = ((DateTime)dpWeek.SelectedDate).AddDays(index * 7);
        }

        private void dpWeek_SelectedDateChanged(object sender, SelectionChangedEventArgs e)
        {
            var newDate = (DateTime)e.AddedItems[0];

            while (newDate.DayOfWeek != dpWeek.FirstDayOfWeek)
                newDate = newDate.AddDays(-1);

            dpWeek.SelectedDate = newDate;
            SelectedWeek = dpWeek.SelectedDate.Value;
            Read();
        }

        private void btnCreate_Click(object sender, RoutedEventArgs e)
        {
            CreateWindow createwindow = new CreateWindow();
            createwindow.Owner = this;
            createwindow.ShowDialog();
            Read();
        }

        public static void Create(string lesnaam, string klas, string datum, string fromtime, string totime)
        {
            MySqlConnection connection = Connect();
            MySqlCommand cmd = new MySqlCommand(@"INSERT INTO rooster (les, klas, datum, begintijd, eindtijd) VALUES (@les, @klas, @datum, @begintijd, @eindtijd)", connection);
            cmd.Parameters.AddWithValue("@les", lesnaam);
            cmd.Parameters.AddWithValue("@klas", klas);
            cmd.Parameters.AddWithValue("@datum", datum);
            cmd.Parameters.AddWithValue("@begintijd", fromtime);
            cmd.Parameters.AddWithValue("@eindtijd", totime);
            connection.Open();
            cmd.ExecuteNonQuery();
            connection.Close();
        }

        private void Read()
        {
            MySqlConnection connection = Connect();
            MySqlCommand cmd;

            

            for (int i = 0; i < lists.Count; i++)
            {
                lists[i].Items.Clear();
                DataTable dt_rooster = new DataTable();
                cmd = new MySqlCommand("SELECT * FROM `rooster` WHERE `datum`=@datum AND `klas`=@klas ORDER BY `begintijd` ASC", connection);
                DateTime day = dpWeek.SelectedDate.Value.AddDays(i);
                ListBoxItem item = new ListBoxItem();
                item.Focusable = false;
                item.Content = day.Date.ToString("yyyy/MM/dd");
                lists[i].Items.Add(item);
                cmd.Parameters.AddWithValue("@datum", day.ToString("yyyy/MM/dd"));
                cmd.Parameters.AddWithValue("@klas", cbKlas.SelectedItem);
                connection.Open();
                dt_rooster.Load(cmd.ExecuteReader());
                connection.Close();
                foreach (DataRow row in dt_rooster.Rows)
                {
                    string begintijd = row["begintijd"].ToString().Insert(2,":");
                    string eindtijd = row["eindtijd"].ToString().Insert(2,":");
                    ListBoxItem listBoxItem = new ListBoxItem();
                    listBoxItem.Tag = row["id"];
                    listBoxItem.Content = $"{begintijd}-{eindtijd} \n {row["les"]} \n {row["klas"]}";
                    lists[i].Items.Add(listBoxItem);
                }
            }
            
        }

        public static void Update(string lesnaam, string klas, string datum, string fromtime, string totime)
        {
            MySqlConnection connection = Connect();
            MySqlCommand cmd = new MySqlCommand(@"UPDATE rooster SET klas = @klas, les = @les, begintijd = @begintijd, eindtijd = @eindtijd WHERE `id`=@id", connection);
            cmd.Parameters.AddWithValue("@les", lesnaam);
            cmd.Parameters.AddWithValue("@klas", klas);
            cmd.Parameters.AddWithValue("@datum", datum);
            cmd.Parameters.AddWithValue("@begintijd", fromtime);
            cmd.Parameters.AddWithValue("@eindtijd", totime);
            cmd.Parameters.AddWithValue("@id", MainWindow.SelectedListBox.Tag);
            connection.Open();
            cmd.ExecuteNonQuery();
            connection.Close();

        }

        public static void Delete(string id)
        {
            MySqlConnection connection = Connect();
            MySqlCommand cmd = new MySqlCommand(@"DELETE FROM rooster WHERE id = @id", connection);
            cmd.Parameters.AddWithValue("@id", id);
            connection.Open();
            cmd.ExecuteNonQuery();
            connection.Close();
        }

        private void SelectionChanged(object sender, RoutedEventArgs e)
        {
            ListBox listbox = sender as ListBox;
            if (listbox.SelectedItem != null)
            {
                SelectedListBox = (ListBoxItem)listbox.SelectedItem;
                UpdateWindow updatewindow = new UpdateWindow();
                updatewindow.ShowDialog();
                Read();
                listbox.SelectedItem = null;
            }
        }



        private void cbKlas_SelectionChanged(object sender, SelectionChangedEventArgs e)
        {
            SelectedKlas = (sender as ComboBox).SelectedItem.ToString();
            Read();
        }
    }
}
